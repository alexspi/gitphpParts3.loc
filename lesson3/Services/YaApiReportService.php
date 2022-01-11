<?php


namespace App\Services;

use App\Http\Controllers\Api\YandexApi;
use App\Models\ClientYaAdGroups;
use App\Models\ClientYaCompany;
use App\Models\ClientYaKeyword;
use App\Models\User;
use App\Models\YandexKeywordReport;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Arr;
use Str;

class YaApiReportService
{


    public function getYandexData($id)
    {

        $api = new YandexApi();
        $ComId = [];


        $user = User::whereId($id)->first();
        $clientlogin = $user->profileclient->yandexprofile->client_ya_login;

        $api->Init($clientlogin);
        $response = $api->getCampaingAll();
        foreach ($response->getCampaigns() as $item) {
// тело цикла вынсти в репозиторий
            if ($item->getDailybudget() === null) {
                $dailyBudgetamount = NULL;
                $dailyBudgetmode = null;
            } else {
                $dailyBudgetamount = $item->getDailybudget()->getAmount() / 1000000;
                $dailyBudgetmode = $item->getDailybudget()->getMode();
            }

            ClientYaCompany::upsert([
                'id' => $item->getId(),
                'client_id' => $user->profileclient->yandexprofile->client_id,
                'company_name' => $item->getName(),
                'start_date' => $item->getStartdate(),
                'type' => $item->getType(),
                "status_payment" => $item->getStatuspayment(),
                "statistics_clicks" => $item->getStatistics()->getClicks(),
                'statistics_impressions' => $item->getStatistics()->getImpressions(),
                'dailyBudget_amount' => $dailyBudgetamount,
                'dailyBudget_mode' => $dailyBudgetmode,
                'funds_shared_spend' => $item->getFunds()->getSharedaccountfunds()->getSpend() / 1000000,

            ], ['id', 'client_id', 'company_name'], ["statistics_clicks", 'statistics_impressions', 'dailyBudget_amount', 'funds_shared_spend']
            );

        }
        $companyId = ClientYaCompany::select('id')->where('client_id', $user->profileclient->yandexprofile->client_id)->get()->toArray();

        foreach ($companyId as $item) {

            $ComId[] = $item['id'];
        }

        $response = $api->getGroupsAll($ComId);

        foreach ($response->getAdgroups() as $item) {
            // тело цикла вынсти в репозиторий
            ClientYaAdGroups::upsert([
                'id' => $item->getId(),
                'company_id' => $item->getCampaignid(),
                'groupe_name' => $item->getName(),
                'type' => $item->getType(),
                "status" => $item->getStatus(),
                'regionIds' => json_encode($item->getRegionIds()),
                "negative_keywords" => json_encode($item->getNegativekeywords()),
                'tracking_params' => json_encode($item->getTrackingparams()),
            ], ['id', 'company_id', 'groupe_name'], ["regionIds", 'negative_keywords', 'tracking_params']
            );

        }

        $response = $api->getKeywordsList($ComId);

        foreach ($response->getKeywords() as $item) {
            // тело цикла вынсти в репозиторий
            if ($item->getStatisticssearch()) {
                $statisticsSearch_clicks = $item->getStatisticssearch()->getClicks();
                $statisticsSearch_impressions = $item->getStatisticssearch()->getImpressions();
            } else {
                $statisticsSearch_clicks = 0;
                $statisticsSearch_impressions = 0;
            }
            if ($item->getStatisticsnetwork()) {
                $statisticsNetwork_clicks = $item->getStatisticsnetwork()->getClicks();
                $statisticsNetwork_impressions = $item->getStatisticsnetwork()->getImpressions();
            } else {
                $statisticsNetwork_clicks = 0;
                $statisticsNetwork_impressions = 0;
            }
            ClientYaKeyword::upsert([
                'id' => $item->getId(),
                'adGroup_id' => $item->getAdgroupid(),
                'company_id' => $item->getCampaignid(),
                'keyword' => $item->getKeyword(),
                'bid' => $item->getBid() / 1000000,
                'contextBid' => $item->getContextbid() / 1000000,
                "strategyPriority" => $item->getStrategypriority(),
                "status" => $item->getStatus(),
                'userParam1' => $item->getUserparam1(),
                'userParam2' => $item->getUserparam2(),
                'productivity' => $item->getProductivity(),
                'statisticsSearch_clicks' => $statisticsSearch_clicks,
                'statisticsSearch_impressions' => $statisticsSearch_impressions,
                "statisticsNetwork_clicks" => $statisticsNetwork_clicks,
                "statisticsNetwork_impressions" => $statisticsNetwork_impressions,
                'servingStatus' => $item->getServingstatus(),
            ], ['id', 'company_id', 'company_id', 'keyword'], [
                    "bid",
                    'contextBid',
                    'strategyPriority',
                    'status',
                    'userParam1',
                    'userParam2',
                    'productivity',
                    'statisticsSearch_clicks',
                    'statisticsNetwork_impressions',
                    'servingStatus']
            );

        }

    }

    public function updateGoals(User $user, $clientcount, $token)
    {

        $goals = [];
        $url = 'https://api-metrika.yandex.net/management/v1/counter/' . $clientcount . '/goals';

        $client = new GuzzleClient([
            'headers' => [
                'Content-Type' => 'application/x-yametrika+json',
                'Authorization' => 'OAuth ' . $token,
            ],
        ]);

        $response = $client->request('GET', $url);

        $result = json_decode($response->getBody(), true);

        foreach ($result['goals'] as $goal) {
            if ($goal['name'] == 'reklama_order'
                or Str::contains($goal['name'], ['отправка формы',
                    'клик',
                    'переход в ',
                    'по номеру',
                    'скачивание файла'])) {
                $goals[] = $goal['id'];
            }
        }

        $yaprofile = $user->profileclient->yandexprofile;

        $yaprofile->ya_goals = $goals;
        $yaprofile->update();

        return $goals;

    }

    public function getYareport(User $user, $token, $companies, $goals, $period)
    {
        $linearray = [];
        $reportname = $user->profileclient->yandexprofile->client_ya_login . '_' . Carbon::now()->format('Y-m-d-H:i');

        $fieldKeyword = [
            "Date",
            "AdGroupId",
            "AdNetworkType",
            "Criteria",
            "CriteriaId",
            "CriteriaType",
            "AvgClickPosition",
            "AvgCpc",
            "AvgEffectiveBid",
            "AvgImpressionPosition",
            "CampaignId",
            "CampaignName",
            "Clicks",
            "ConversionRate",
            "Conversions",
            "Cost",
            "CostPerConversion",
            'Ctr',
            'GoalsRoi',
            'Impressions',
            'Placement',
            'Revenue',
            'WeightedCtr',
            'WeightedImpressions'
        ];

        $params = [
            "params" => [
                "SelectionCriteria" => [
                    "Filter" => [

                        [
                            "Field" => "CampaignId",
                            "Operator" => "IN",
                            "Values" => $companies
                        ]
                    ]],
                "Goals" => $goals,
                "FieldNames" => $fieldKeyword,
                "OrderBy" => [[
                    "Field" => "Date"
                ]],
                "ReportName" => "$reportname",
                "ReportType" => "CUSTOM_REPORT",
                "DateRangeType" => "$period",
                "Format" => "TSV",
                "IncludeVAT" => "YES"
            ]
        ];

        $body = json_encode($params);

        $responseBody = $this->requestYa($token, $user->profileclient->yandexprofile->client_ya_login, $body);

        $str = preg_replace("/Total rows:(.*)/", "", $responseBody);
        $str = explode("\n", $str);
        unset($str[0]);
        $key = explode("\t", $str[1]);
        unset($str[1]);

        foreach ($str as $line) {
            if (!empty($line)) {
                $linearray[] = array_combine($key, explode("\t", $line));
            }
        };

        return $linearray;
    }

    public function updateData(User $user, $cheat, $goals, $linearray)
    {


        if (count($cheat) == 1) {
            $nakrutka = $user->profileclient->yandexprofile->yacheat['0']->base_cheat;
            $this->saveResult($user, $goals, $linearray, $nakrutka);
        } else {
            foreach ($cheat as $ch) {
                if ($ch->new_cheat == null) {
                    $date = $ch->cheat_date;
                    $nakrutka = $ch->base_cheat;

                    $this->saveResult($user, $goals, $linearray, $nakrutka);

                } else {
                    $date = $ch->cheat_date;
                    $nakrutka = $ch->new_cheat;

                    $filtered = Arr::where($linearray, function ($value, $key) use ($date) {
                        return $value['Date'] >= $date;
                    });

                    $this->saveResult($user, $goals, $filtered, $nakrutka);
                }

            }
        }


    }

    public function cleanUserData(User $user)
    {
        $companys = ClientYaCompany::where('client_id', $user->profileclient->id)->get();

        foreach ($companys as $company) {
            YandexKeywordReport::where('campaign_id', $company->id)->delete();
            ClientYaKeyword::where('company_id', $company->id)->delete();
            ClientYaAdGroups::where('company_id', $company->id)->delete();
        }
        ClientYaCompany::where('client_id', $user->profileclient->id)->delete();

    }

    function saveResult(User $user, $goals, $linearray, $nakrutka)
    {

        foreach ($linearray as $item) {
            if ($item['AdGroupId'] !== '--') {
                if ($item['AvgClickPosition'] == '--') {
                    $AvgClickPosition = null;
                } else {
                    $AvgClickPosition = $item['AvgClickPosition'];
                };
                if ($item['AvgCpc'] == '--') {
                    $AvgCpc = null;
                } else {
                    $AvgCpc = $item['AvgCpc'];
                };
                if ($item['AvgEffectiveBid'] == '--') {
                    $AvgEffectiveBid = null;
                } else {
                    $AvgEffectiveBid = $item['AvgEffectiveBid'];
                };
                if ($item['AvgImpressionPosition'] == '--') {
                    $avgImpressionPosition = null;
                } else {
                    $avgImpressionPosition = $item['AvgImpressionPosition'];
                };
                $conversionRate = [];
                $conversions = [];
                $costPerConversion = [];
                $goalsRoi = [];
                $revenue = [];

                foreach ($goals as $goal) {
                    $keyRate = 'ConversionRate_' . $goal . '_LSC';
                    $keyConv = 'Conversions_' . $goal . '_LSC';
                    $keyPerConv = 'CostPerConversion_' . $goal . '_LSC';
                    $keyRoi = 'GoalsRoi_' . $goal . '_LSC';
                    $keyReven = 'Revenue_' . $goal . '_LSC';

                    $conversionRate = Arr::add($conversionRate, $keyRate, $item[$keyRate]);
                    $conversions = Arr::add($conversions, $keyConv, $item[$keyConv]);
                    $costPerConversion = Arr::add($costPerConversion, $keyPerConv, $item[$keyPerConv]);
                    $goalsRoi = Arr::add($goalsRoi, $keyRoi, $item[$keyRoi]);
                    $revenue = Arr::add($revenue, $keyReven, $item[$keyReven]);

                }
//            dd($conversionRate);
                YandexKeywordReport::updateOrInsert([
                    'daterep' => $item['Date'],
                    'adGroup_id' => $item['AdGroupId'],
                    'keyword_id' => $item['CriteriaId'],
                    'campaign_id' => $item['CampaignId'],
                    'placement' => $item['Placement'],
                    "criterion" => $item['Criteria'],
                    "criterionType" => $item['CriteriaType'],
                ],
                    ['avgClickPosition' => $AvgClickPosition,
                        'avgCpc' => $AvgCpc * $nakrutka,
                        'avgEffectiveBid' => $AvgEffectiveBid,
                        'avgImpressionPosition' => $avgImpressionPosition,
                        'clicks' => $item['Clicks'],
                        'conversionRate' => json_encode($conversionRate),
                        'conversions' => json_encode($conversions),
                        'cost' => $item['Cost'],
                        'costPerConversion' => json_encode($costPerConversion),
                        'ctr' => $item['Ctr'],
                        'goalsRoi' => json_encode($goalsRoi),
                        'impressions' => $item['Impressions'],
                        'revenue' => json_encode($revenue),
                        'weightedCtr' => $item['WeightedCtr'],
                        'weightedImpressions' => $item['WeightedImpressions']
                    ]);
            }
        }
    }

    /**
     *
     * излишний код
     * @param $token
     * @param $clientLogin
     * @param $body
     * @return false|string
     */
    public function requestYa($token, $clientLogin, $body)
    {

        $url = 'https://api.direct.yandex.com/json/v5/reports';

        $headers = array(
            "Authorization: Bearer $token",
            "Client-Login: $clientLogin",
            "Accept-Language: ru",
            "processingMode: auto",
            "returnMoneyInMicros: false",
            // Не выводить в отчете строку с названием отчета и диапазоном дат
            // "skipReportHeader: true",
            // Не выводить в отчете строку с названиями полей
            // "skipColumnHeader: true",
            // Не выводить в отчете строку с количеством строк статистики
            //  "skipReportSummary: true"
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($curl, CURLOPT_CAINFO, getcwd().'\CA.pem');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//dd($headers);
        while (true) {

            $result = curl_exec($curl);

            if (!$result) {

                echo('Ошибка cURL: ' . curl_errno($curl) . ' - ' . curl_error($curl));

                break;

            } else {

                // Разделение HTTP-заголовков и тела ответа
                $responseHeadersSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
                $responseHeaders = substr($result, 0, $responseHeadersSize);
                $responseBody = substr($result, $responseHeadersSize);

                // Получение кода состояния HTTP
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                // Извлечение HTTP-заголовков ответа
                // Идентификатор запроса
                $requestId = preg_match('/RequestId: (\d+)/', $responseHeaders, $arr) ? $arr[1] : false;
                //  Рекомендуемый интервал в секундах для проверки готовности отчета
                $retryIn = preg_match('/retryIn: (\d+)/', $responseHeaders, $arr) ? $arr[1] : 60;

                if ($httpCode == 400) {

                    echo "Параметры запроса указаны неверно или достигнут лимит отчетов в очереди<br>";
                    echo "RequestId: {$requestId}<br>";
                    echo "JSON-код запроса:<br>{$body}<br>";
                    echo "JSON-код ответа сервера:<br>{$responseBody}<br>";

                    break;

                } elseif ($httpCode == 200) {

                    echo "Отчет создан успешно<br>";
                    echo "RequestId: {$requestId}<br>";
//                   dump($responseBody);

                    break;

                } elseif ($httpCode == 201) {

                    echo "Отчет успешно поставлен в очередь в режиме офлайн<br>";
                    echo "Повторная отправка запроса через {$retryIn} секунд<br>";
                    echo "RequestId: {$requestId}<br>";

                    sleep($retryIn);

                } elseif ($httpCode == 202) {

                    echo "Отчет формируется в режиме offline.<br>";
                    echo "Повторная отправка запроса через {$retryIn} секунд<br>";
                    echo "RequestId: {$requestId}<br>";

                    sleep($retryIn);

                } elseif ($httpCode == 500) {

                    echo "При формировании отчета произошла ошибка. Пожалуйста, попробуйте повторить запрос позднее<br>";
                    echo "RequestId: {$requestId}<br>";
                    echo "JSON-код ответа сервера:<br>{$responseBody}<br>";

                    break;

                } elseif ($httpCode == 502) {

                    echo "Время формирования отчета превысило серверное ограничение.<br>";
                    echo "Пожалуйста, попробуйте изменить параметры запроса - уменьшить период и количество запрашиваемых данных.<br>";
                    echo "RequestId: {$requestId}<br>";

                    break;

                } else {

                    echo "Произошла непредвиденная ошибка.<br>";
                    echo "RequestId: {$requestId}<br>";
                    echo "JSON-код запроса:<br>{$body}<br>";
                    echo "JSON-код ответа сервера:<br>{$responseBody}<br>";

                    break;

                }
            }
        }

        curl_close($curl);
//        dd($responseBody);
        return $responseBody;
    }
}
