<?php

namespace App\Http\Controllers;

use App\Models\ClientYaProfile;
use App\Models\YandexKeywordReport;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Null_;
use YandexMetrika;
use App\Http\Controllers\Api\YandexApi;
use App\Models\ClientYaAdGroups;
use App\Models\ClientYaCompany;
use App\Models\ClientYaKeyword;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use App\Services\YaApiReportService;

class ApiDirectController extends Controller
{

    private $api;

    public function __construct(YandexApi $api)
    {
        $this->api = $api;

    }

    /**
     * метод использовался 1 раз для получения доступов к яндекс API
     * можно убрать совсем
     * @param $code
     * @return mixed
     */
    public function getCode($code = "")
    {

        $client_id = env('APIDIRECT_CLIENT_ID');
        $client_secret = env('APIDIRECT_CLIENT_SECRET');

        try {
            $client = new Client();

            $result = $client->request('POST', 'https://oauth.yandex.ru/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => '2039174',
                    'client_id' => $client_id,
                    'client_secret' => $client_secret
                ],
                'headers' => [
                    "Content-type", "application/x-www-form-urlencoded"
                ],

            ]);

            return view("api.token", [
                "token" => json_decode($result->getBody()->getContents())->access_token
            ]);
        } catch (ClientException $e) {
            return view("api.token", [
                "error" => $e,
                "link" => 'https://oauth.yandex.ru/authorize?response_type=code&client_id=' . $client_id
            ]);
        }


    }


    /**
     * @param $id
     * @return void
     */
    public function getYandexData($id)
    {
        $ComId = [];

        $user = User::whereId($id)->first();
        $clientlogin = $user->profileclient->yandexprofile->client_ya_login;
//  переменную $clientlogin вынести в GlobalScoupe Модели User
        $this->api->Init($clientlogin);
        $response = $this->api->getCampaingAll();

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
//        строки 117-121 вынести в репозиторий
        $companyId = ClientYaCompany::select('id')->where('client_id', $user->profileclient->yandexprofile->client_id)->get()->toArray();

        foreach ($companyId as $item) {
            $ComId[] = $item['id'];
        }

        $response = $this->api->getGroupsAll($ComId);

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

        $response = $this->api->getKeywordsList($ComId);

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

    /**
     * @param Request $request
     * @return void
     */
    public function getYandexReport(Request $request)
    {
        $user = User::whereId($request->userid)->first();
        $companies = [];
        $linearray = [];
		$this->getYandexData($user->id);
		
		$UpdateYa = new YaApiReportService();

        $clientcount = $user->profileclient->yandexprofile->ya_metrika;
        $token = config('api.YA_API_TOKEN');
        $compains = $user->profileclient->yandexcompany;

        foreach ($compains as $fff) {
            $companies[] = $fff->id;
        }

        $goals = $UpdateYa->updateGoals($user, $clientcount, $token);

        $linearray = $UpdateYa->getYareport($user, $token, $companies, $goals, $request->period);

        $cheat = $user->profileclient->yandexprofile->yacheat;

        $UpdateYa->updateData($user, $cheat, $goals, $linearray);


        activity('clientCompany')
            ->causedBy(auth()->user())
            ->withProperties(['cli' => $user->profileclient->yandexprofile->client_ya_login])
            ->log('update');


    }


}
