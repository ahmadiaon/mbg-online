<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\DatabaseData;
use App\Models\DatabaseDataSource;
use App\Models\DatabaseField;
use App\Models\DatabaseFieldShow;
use App\Models\DatabaseTable;
use App\Models\GroupForm;
use App\Models\User;
use App\Models\UserTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseDataController extends Controller
{

    public static function getrefreshSession()
    {
        DatabaseDataController::refreshSessionProses('ABC');
    }
    public static function refreshSessionProses($auth_login)
    {

        $users = User::where('auth_login', json_decode($auth_login))->first();
        if ($auth_login == 'ABC') {
            $users = User::where('nrp', 'MBLE-0422003')->first();
        }

        $Q_user_feture = DatabaseData::where('code_table_data', 'KARYAWAN-ACCESS-FEATURE')
            ->where('code_data', 'like', ResponseFormatter::toUUID($users->nrp) . '%')
            ->where('code_field_data', 'FEATURE')
            ->get();
        $arr_data_feature = [];
        foreach ($Q_user_feture as $data_user_feture) {
            if (!in_array($data_user_feture->value_data, $arr_data_feature)) {
                $arr_data_feature[] =  $data_user_feture->value_data;
            }
        }


        $FILTER_APP = [
            'DEFAULT_FILTER' => [
                'day' => date('d'),
                'month' => date('m'),
                'year' => date('Y'),
                "date_start" => "2025-09-01",
                "date_end" => "2025-09-30",
                "statusKaryawan" => [
                    "AKTIVE",
                    "PHK-BULAN-INI"
                ],
                "FEATURE" => $arr_data_feature,
                "PERUSAHAAN" => [
                    "PT--MBLE",
                    "PT--KPN",
                    "PT--MB",
                    "CV--BK"
                ],
                "PROJECT" => [
                    "MBG",
                    "HAULING",
                    "WORKSHOP",
                    "HAULING-PT-TOP",
                    "IUP-CV--BK",
                    "IUP-PT--MB",
                    "KONTRAKTOR-PT--MB",
                    "PORT",
                    "EXTERNAL",
                    "CRUSHER",
                    "PROJEK-UMUM",
                    "UMUM",
                    "KBU",
                    "TOP",
                    "EX",
                    "PENDANG",
                    "HRGA",
                    "WAYANG",
                    "EKSTERNAL"
                ],
                "DEPARTEMEN" => [
                    "HRGA",
                    "HAULING",
                    "PRODUKSI",
                    "ENGINEERING",
                    "HSE",
                    "PLANT",
                    "PORT",
                    "LOGISTIC",
                    "PLAN",
                    "FINANCE",
                    "MANAJEMEN",
                    "MECHANIC",
                    "CIVIL",
                    "ERS",
                    "FA-&-TAX",
                    "BOD",
                    "MBG",
                    "CRUSHER",
                    "KONTRAKTOR-PT--MB",
                    "UMUM",
                    "PORT---ROOM-17",
                    "WAYANG",
                    "EKSTERNAL"
                ],
                "DIVISI" => [
                    "IT",
                    "HR",
                    "NON",
                    "ENGINEERING",
                    "HAULING",
                    "HRGA",
                    "FA-&-TAX",
                    "GA",
                    "HSE",
                    "MEDIC",
                    "PLANT",
                    "PORT",
                    "SAFETY",
                    "PRODUKSI",
                    "ENVIRO",
                    "MANAJEMEN",
                    "ELECTRICK",
                    "CRUSHER",
                    "MECHANIC",
                    "EKSTERNAL",
                    "FUEL",
                    "ROAD-MAINTENACE",
                    "WELDER",
                    "LATHE",
                    "SPARE-PART",
                    "SERVICE-MAINTENANCE",
                    "PLAN",
                    "SUPPORT",
                    "SURVEY",
                    "TYRE",
                    "EKTERNAL",
                    "SAWIT",
                    "SECURITY",
                    "MBG",
                    "EXTERNAL",
                    "KONTRAKTOR-PT--MB",
                    "PROJEK-UMUM",
                    "UMUM",
                    "WAYANG"
                ],
                "KARYAWAN" => [
                    "MBLE-0422003",
                    "MBLE-240102",
                    "MBLE-240473",
                    "MBLE-250211",
                    "MBLE-210508",
                    "MB-PL-100012",
                    "MBLE-210496",
                    "MB-HO-200090",
                    "MB-HO-210092",
                    "MBLE-0219070054",
                    "MBLE-240127",
                    "MB-HO-140036",
                    "MB-F01-150069",
                    "MBLE-2411139",
                    "MBLE-240115",
                    "MBLE-230921",
                    "MB-250106",
                    "MB-F01-200255",
                    "MB-F01-150065",
                    "MB-PL-250106",
                    "MBLE-0422027",
                    "MBLE-220591",
                    "MBLE-200369",
                    "MBLE-05230385",
                    "MB-F01-190172",
                    "MBLE-2408103",
                    "MB-PL-2411137",
                    "MB-PL-2411138",
                    "MB-PL-250103",
                    "MBLE-250487",
                    "MBLE-250332",
                    "MBLE-2509230",
                    "MBLE-240116",
                    "MBLE-220822",
                    "MBLE-110027",
                    "MBLE-220528",
                    "MBLE-110031",
                    "MBLE-0321100002",
                    "MB-F01-130045",
                    "MB-F01-170109",
                    "MB-F01-180153",
                    "MBLE-230920",
                    "MB-F01-180157",
                    "MBLE-230926",
                    "MBLE-240117",
                    "MBLE-240126",
                    "MBLE-250435",
                    "MBLE-2505137",
                    "MBLE-2507186",
                    "MBLE-2508203",
                    "MBLE-231001",
                    "MBLE-0422081",
                    "MBLE-2409111",
                    "MBLE-2412141",
                    "MBLE-2505130",
                    "MBLE-2508202",
                    "MBLE-230852",
                    "MBLE-250324",
                    "MBLE-250327",
                    "MB-2505125",
                    "MBLE-2505126",
                    "MBLE-2505127",
                    "MBLE-2508190",
                    "MBLE-2509221",
                    "MBLE-080006",
                    "MBLE-230929",
                    "MBLE-240104",
                    "MBLE-230910",
                    "MBLE-062302010",
                    "MBLE-240791",
                    "MBLE-2504107",
                    "MB-MG-2508201",
                    "MBLE-230871",
                    10924,
                    150924,
                    230924,
                    "MBLE-231204",
                    "MB-PL---2409117",
                    "MBLE-220608",
                    "MB-PL-170232",
                    "MB-HO-180052",
                    "MBLE-231110",
                    "MBLE-0218120001",
                    "MBLE-200375",
                    "MBLE-120020",
                    "MB-FO-170342",
                    "MB-F0-170342",
                    "MBLE-220535",
                    "MBLE-220718",
                    "MB-F01-130037",
                    "MB-F01-160099",
                    "MBLE-110021",
                    "MB-PL-2410123",
                    "MB-PL-250105",
                    "MB-PL-250481",
                    "MBLE-2411132",
                    "MBLE-0422083",
                    "BK-PL-220367",
                    "MB-PL-240796",
                    "MB-PL-250101",
                    "MB-PL-2509206",
                    "MBLE-230845",
                    "MB-250481",
                    "MBLE-2504112",
                    "MBLE-0422001",
                    "MB-PL-2410129",
                    "MB-HO-100014",
                    "MBLE-250436",
                    "MBLE-250329",
                    "MBLE-250484",
                    "MBLE-250437",
                    "MBLE-250439",
                    "MBLE-250440",
                    "MBLE-250442",
                    "MBLE-250445",
                    "MBLE-250447",
                    "MBLE-250448",
                    "MBLE-250449",
                    "MBLE-250451",
                    "MBLE-250452",
                    "MBLE-250453",
                    "MBLE-250456",
                    "MBLE-250459",
                    "MBLE-250460",
                    "MBLE-250461",
                    "MBLE-250463",
                    "MBLE-250464",
                    "MBLE-250465",
                    "MBLE-250466",
                    "MBLE-250468",
                    "MBLE-250469",
                    "MBLE-250470",
                    "MBLE-250403",
                    "MBLE-250471",
                    "MBLE-250472",
                    "MBLE-250473",
                    "MBLE-250474",
                    "MBLE-250475",
                    "MBLE-2504106",
                    "MBLE-2508192",
                    "MBLE-2508198",
                    "MBLE-2508204",
                    "MBLE-2508205",
                    "MBLE-2509210",
                    "MBLE-2509211",
                    "MBLE-2509212",
                    "MBLE-2509231",
                    "MBLE-2509232",
                    "MBLE-2509233",
                    "MBLE-2509234",
                    "MB-PL-2509220",
                    "MB-PL-2509227",
                    "MBLE-2507154",
                    "MBLE-2507155",
                    "MBLE-2507157",
                    "MBLE-2507162",
                    "MBLE-2507163",
                    "MBLE-2507182",
                    "MBLE-2507183",
                    "MB-HO-050007",
                    "MB-HO-040006",
                    "MB-HO-180051",
                    "MBLE-0422002",
                    "MB-HO-130032",
                    "MB-HO-130028",
                    "MB-HO-200063",
                    "MBLE-240132",
                    "MBLE-140143",
                    "MBLE-240359",
                    "MBLE-2504101",
                    "MBLE-220595",
                    "MBLE-210431",
                    "MBLE-220596",
                    "MB-PL-120046",
                    "MBLE-250488",
                    "MBLE-220761",
                    "MBLE-240240",
                    "MBLE-240239",
                    "MBLE-062304024",
                    "MBLE-2411130",
                    "MBLE-2411131",
                    "MBLE-2411133",
                    "MBLE-2411134",
                    "MB-MG-2507174",
                    "MB-MG-2507177",
                    "MB-PL-220524",
                    "MBLE-210416",
                    "MB-F01-160093",
                    "MB-PL-2509208",
                    "MBLE-250490",
                    "MBLE-2504109",
                    "MBLE-2504104",
                    "MBLE-2507160",
                    "MBLE-2505119",
                    "MB-MG-2507172",
                    "MB-MG-2507173",
                    "MBLE-0219010015",
                    "MBLE-210517",
                    "MBLE-220769",
                    "MBLE-220678",
                    "MBLE-220660",
                    "MBLE-220659",
                    "MBLE-240246",
                    "MBLE-2408106",
                    "MBLE-2411140",
                    "MBLE-220758",
                    "MBLE-220533",
                    "MB-F01-200221",
                    "MBLE-220669",
                    "MBLE-05230168",
                    "MBLE-231206",
                    "MBLE-240133",
                    "MBLE-240256",
                    "MBLE-250217",
                    "MBLE-0422049",
                    "MBLE-0422051",
                    "MB-PL-2509207",
                    "MBLE-220540",
                    "MBLE-220754",
                    "MBLE-130069",
                    "MBLE-220642",
                    "MBLE-220604",
                    "MBLE-210447",
                    "MBLE-220531",
                    "MBLE-170227",
                    "BK-PL-170184",
                    "MBLE-220727",
                    "MB-PL-210420",
                    "MBLE-05230281",
                    "MBLE-220639",
                    "MBLE-220594",
                    "MBLE-220719",
                    "MBLE-052212047",
                    "MBLE-241227",
                    "MB-F01-200236",
                    "MBLE-230858",
                    "MBLE-2409113",
                    "MBLE-190328",
                    "MB-F0-150147",
                    "BK-PL-120059",
                    "MBLE-220681",
                    "MBLE-0219110098",
                    "MB-F01-170121",
                    "MBLE-210430",
                    "MBLE-210471",
                    "MBLE-220729",
                    "MBLE-210482",
                    "MBLE-210429",
                    "MBLE-0219100080",
                    "MB-F01-150075",
                    "MBLE-0422030",
                    "MB-PL-250214",
                    "MBLE-220616",
                    "MBLE-250218",
                    "MBLE-2507158",
                    "MBLE-2507159",
                    "MBLE-170239",
                    "MBLE-220686",
                    "MBLE-220607",
                    "MBLE-210428",
                    "MBLE-200382",
                    "MBLE-240583",
                    "MBLE-240585",
                    "MBLE-080005",
                    "MBLE-210437",
                    "MBLE-05230284",
                    "MBLE-052211020",
                    "MBLE-220530",
                    "MBLE-190343",
                    "MBLE-0322010063",
                    "MBLE-0422010",
                    "MBLE-220711",
                    "MBLE-220775",
                    "MBLE-230867",
                    "MB-F01-160101",
                    "MBLE-062307028",
                    "MBLE-0422042",
                    "MBLE-250215",
                    "MB-F01-190179",
                    "MBLE-0219120106",
                    "MBLE-170247",
                    "MBLE-220776",
                    "MB-F01-110023",
                    "MBLE-2505131",
                    "MBLE-231201",
                    "MBLE-220560",
                    "MBLE-130100",
                    "MBLE-140138",
                    "MB-F01-150087",
                    "MBLE-0220010109",
                    "MB-HO-190062",
                    "MB-HO-080010",
                    "MB-HO-100015",
                    "MBLE-210391",
                    "MBLE-231006",
                    "MBLE-140123",
                    "MB-F01-150072",
                    "MBLE-120042",
                    "MBLE-160163",
                    "MBLE-220691",
                    "MBLE-220707",
                    "MBLE-220791",
                    "MBLE-230881",
                    "MBLE-210390",
                    "MBLE-0321100010",
                    "MBLE-240581",
                    "MBLE-240797",
                    "MBLE-250325",
                    "MBLE-0422022",
                    "MB-PL-250212",
                    "MBLE-250326",
                    "MBLE-0422005",
                    "MBLE-100014",
                    "MBLE-0321110039",
                    "MBLE-2409112",
                    "MBLE-2409114",
                    "MBLE-2505143",
                    "MBLE-110024",
                    "MBLE-220697",
                    "MBLE-220638",
                    "MBLE-230918",
                    "MBLE-2410127",
                    "MB-MG-2410128",
                    "MBLE-130073",
                    "MBLE-200374",
                    "MBLE-220647",
                    "MBLE-220835",
                    "MBLE-130066",
                    "MB-PL-250110",
                    "MBLE-120056",
                    "MBLE-220703",
                    "MB-F01-200225",
                    "MBLE-200378",
                    "MBLE-240105",
                    "MBLE-240122",
                    "MBLE-240123",
                    "MB-F01-180156",
                    "MBLE-240793",
                    "MBLE-240798",
                    "MBLE-2410120",
                    "MBLE-2410121",
                    "MBLE-2410122",
                    "MBLE-2410126",
                    "MBLE-250483",
                    "MBLE-250485",
                    "MBLE-250486",
                    "MBLE-2507184",
                    "MBLE-2507185",
                    "MB-PL-2410128",
                    "MBLE-2509218",
                    "MBLE-2509219",
                    "MBLE-2507164",
                    "MBLE-2507165",
                    "MBLE-2507166",
                    "MBLE-2507181",
                    "MBLE-2507168",
                    "MB-MG-2507175",
                    "MB-MG-2507176",
                    "MBLE-2507178",
                    "MBLE-2507179",
                    "MBLE-2509214",
                    "MBLE-2509215",
                    "MBLE-2507188",
                    "MBLE-2507189",
                    "MBLE-2508199",
                    "MBLE-2508200",
                    "MBLE-2509222",
                    "MBLE-080007",
                    "MBLE-231003",
                    "MBLE-2504105",
                    "MBLE-2504108",
                    "MBLE-2509209",
                    "MBLE-2509228",
                    "MBLE-2505144",
                    "MB-F01-120034",
                    "MBLE-231002",
                    "MB-PL-130096",
                    "MBLE-0322010050",
                    "MBLE-0322010057",
                    "MB-F01-100014",
                    "MB-F01-080007",
                    "MB-HO-230106",
                    "MB-F01-150058",
                    "MBET-2111010013",
                    "MBLE-240564",
                    "MBLE-170238",
                    "MBLE-210422",
                    "MBLE-210458",
                    "MBLE-200379",
                    "MBLE-130112",
                    "MBLE-210436",
                    "MBLE-240258",
                    "MB-PL-200364",
                    "MB-PL-220527",
                    "MB-PL-220526",
                    "MBLE-240101",
                    "MBLE-170220",
                    "MB-F01-150056",
                    "MB-PL-220829",
                    "MB-PL-120055",
                    "MB-PL-200383",
                    "MBLE-130088",
                    "MB-PL-220818",
                    "MB-PL-240687",
                    "MBLE-240248",
                    "MBLE-190315",
                    "MB-F01-100015",
                    "MB-PL-2409117",
                    "MBLE-2505139",
                    "MBLE-2505140",
                    "MBLE-190340",
                    "MB-PL-230883",
                    "MB-PL-220823",
                    "MB-PL-220826",
                    "MB-PL-220824",
                    "MB-PL-210386",
                    "MB-PL-110035",
                    "MB-PL-170209",
                    "MB-PL-210385",
                    "MB-PL-210387",
                    "MBLE-2505123",
                    "MBLE-2505124",
                    "MB-PL-120054",
                    "MB-PL-100009",
                    "MBLE-230880",
                    "MBLE-220721",
                    "MB-PL-100013",
                    "MBLE-0321100022",
                    "MBLE-2505146",
                    "MBLE-2505132",
                    "MB-PL-210468",
                    "MBLE-220644",
                    "MBLE-190333",
                    "MBLE-180285",
                    "MB-PL-240688",
                    "MBLE-2505122",
                    "MBLE-2505133",
                    "MBLE-2505134",
                    "MBLE-2505145",
                    "MBLE-2507153",
                    "MBLE-052211002",
                    "MB-PL-210388",
                    "MB-PL-200368",
                    "MB-250108",
                    "MB-250109",
                    "MB-PL-250108",
                    "MB-PL-250109",
                    "MBLE-250479",
                    "MBLE-250480",
                    "MBLE-250491",
                    "MBLE-250492",
                    "MBLE-250493",
                    "MBLE-250494",
                    "MBLE-250495",
                    "MBLE-250496",
                    "MBLE-250499",
                    "MBLE-2504100",
                    "MBLE-2504111",
                    "MBLE-250330",
                    "MBLE-250331",
                    "MBLE-2504115",
                    "MBLE-2505117",
                    "MBLE-2505129",
                    "MBLE-2505135",
                    "MBLE-2505138",
                    "MBLE-2505142",
                    "MBLE-2507180",
                    "MBLE-2508191",
                    "MBLE-2509213",
                    "MBLE-2509223",
                    "MBLE-2509224",
                    "MBLE-2509225",
                    "MBLE-2509226",
                    "MB-PL-2509229",
                    "MBLE-2506151",
                    "MBLE-2509216",
                    "MBLE-2506150",
                    "MB-MG-2507169",
                    "MBLE-2508193",
                    "MBLE-2508194",
                    "MBLE-2508195",
                    "MBLE-2509217",
                    "MB-F01-150080",
                    "MBLE-170185",
                    "MBLE-130090",
                    "MBLE-130082",
                    "MBLE-230898",
                    "MBLE-220551",
                    "MBLE-220613",
                    "MBLE-220609",
                    "MBLE-130101",
                    "MBLE-140130",
                    "MBLE-210394",
                    "MBLE-130075",
                    "MBLE-130092",
                    "MBLE-130074",
                    "MBLE-140119",
                    "MBLE-0321110040",
                    "MBLE-240106",
                    "MBLE-240110",
                    "MBLE-240113",
                    "MBLE-240362",
                    "MBLE-210493",
                    "MBLE-240579",
                    "MBLE-240580",
                    "MBLE-2407101",
                    "MBLE-2409108",
                    "MBLE-2409109",
                    "MBLE-2409110",
                    "MBLE-0422061",
                    "MBLE-032110028",
                    "MBLE-250220",
                    "MBLE-2505141",
                    "MBLE-062307025",
                    "MBLE-052212052",
                    "MBLE-05230174",
                    "MBLE-062307026",
                    "MBLE-240253",
                    "MBLE-052212049",
                    "W-6",
                    "MBLE-052211037",
                    "MBLE-220629",
                    "MBLE-05230171",
                    "MBLE-0422062",
                    "MBLE-210426",
                    "MBLE-231005",
                    "MBLE-0220020110",
                    "MBLE-0322010058",
                    "MBLE-062304018",
                    "MBLE-2409116",
                    "MBLE-05230170",
                    "MB-F01-150067",
                    "MBLE-230934",
                    "MBLE-052211011",
                    "MBLE-240251",
                    "MBLE-062302011",
                    "MBLE-220699",
                    "MBLE-220614",
                    "MBLE---2410118",
                    "MBLE-2507187",
                    "MBLE-052212065",
                    "MBLE-2410118",
                    "MBLE-0220010107",
                    "MBLE-05230173",
                    "MBLE-0422069",
                    "MBLE-062307030",
                    "MBLE-05230172",
                    "MBLE-0321120046",
                    "MBLE-230847",
                    "MBLE-230914",
                    "MBLE-2411135",
                    "MB-F01-110022",
                    "MBLE-170205",
                    "MBLE-230906",
                    "MBLE-170233",
                    "MBLE-140131",
                    "MBLE-170249",
                    "MBLE-210396",
                    "MBLE-130086",
                    "MBLE-130102",
                    "MBLE-240109",
                    "MBLE-240111",
                    "MBLE-220538",
                    "BK-PL-130108",
                    "MBLE-210435",
                    290824,
                    "MB-PL-290824",
                    190924,
                    "MB-PL-190924",
                    "MBLE-250221",
                    "MBLE-2507161",
                    "MB-MG-2507170",
                    "MB-MG-2507171",
                    "MBLE-210413",
                    "MBLE-0422082",
                    "MB-HO-210094",
                    "MB-F02-060002",
                    "MB-F02-050001",
                    "MB-F02-150089",
                    "MB-F02-150084",
                    "MB-F02-170111",
                    "MB-F02-180142",
                    "MB-F02-200205",
                    "MB-F02-180145",
                    "MB-F02-180146",
                    "MB-F02-190193",
                    "MB-F02-190159",
                    "MB-F02-190167",
                    "MB-F02-200206",
                    "MB-F02-220213",
                    "MB-F02-170120",
                    "MB-F02-230915",
                    "MB-F02-230916",
                    "MB-F02-220217",
                    "MB-F02-200204",
                    "MB-F02-200201",
                    "MB-DKI-010001",
                    "MBLE-010001",
                    "MB-HO-200089",
                    "MB-HO-200088",
                    "005",
                    "006",
                    "007",
                    "008",
                    "MB-CN-010004",
                    "MB-HO-010001",
                    "012",
                    "013",
                    "014",
                    "015",
                    "MB-HO-210314"
                ],
                "JABATAN" => [
                    "SENIOR-OFFICER-MIS",
                    "CIVIL-CREW",
                    "CIVIL-MAINTENANCE-COORDINATOR",
                    "LV-DRIVER",
                    "GENERAL-AFFAIR-GROUP-LEADER",
                    "GARDEN-KEEPER",
                    "GENERAL-AFFAIR-OFFICER",
                    "GENERAL-WORKER",
                    "GENERAL-AFFAIR-JR--OFFICER",
                    "KOKI",
                    "LAUNDRY",
                    "USTADZ",
                    "JUNIOR-WELDER",
                    "TEHNICIAN",
                    "GARDEN-WORKER",
                    "PLUMBING-TECHNICIAN",
                    "BUS-DRIVER",
                    "CR-&-LA-PERSONNEL",
                    "HRD-SUPERINTENDENT",
                    "PAYROLL-CLERK",
                    "PAYROLL-OFFICER",
                    "TRAINEER",
                    "HUMAN-RESOURCES-EKSTERNAL-RELATION",
                    "HRD-ADMIN",
                    "HRD-JR--OFFICER",
                    "HR-ADMIN",
                    "INDUSTRIAL-RELATION-SPECIALIST",
                    "PEGAWAI-HARIAN",
                    "ABK",
                    "BULLDOZER-OPERATOR",
                    "CRUSHER-FOREMAN",
                    "DT-DRIVER",
                    "DUMP-MAN",
                    "EXCAVATOR-OPERATOR",
                    "JURAGAN-MB-017",
                    "PORT-CREW",
                    "WASHING-MAN",
                    "WHEEL-LOADER-OPERATOR",
                    "CRUSHER-CREW",
                    "MAINTENANCE-CRUSHER",
                    "FUEL-MONITORING-CONTROL",
                    "TUGBOAT-MOTORIS",
                    "ACCOUNTING-&-TAX",
                    "FINANCE-SENIOR-OFFICER",
                    "OFFICE-SITE-MANAGER",
                    "KEPALA-TEKNIK-TAMBANG",
                    "PROJECT-MANAGER",
                    "GENERAL-MANAGER",
                    "FINANCE-ADMIN",
                    "ENVIRO-CREW",
                    "ENVIROMENT",
                    "NURSERY",
                    "NURSERY-SUPERVISOR",
                    "JUNIOR-ENVIRONMENT",
                    "ENVIRONMENT-&-RECLAMATION-GROUP-LEADER",
                    "HSE-ADMIN",
                    "SAFETY-MAN",
                    "SAFETY-ADMIN",
                    "SAFETY-OFFICER",
                    "HSE-SUPERVISOR",
                    "PARAMEDIC",
                    "JUNIOR-PARAMEDIC",
                    "SAFETY-JR--OFFICER",
                    "MONITORING-CONTROL-JR--OFFICER",
                    "ENGINEER-JR--MINE-PLAN",
                    "MONITORING-CONTROL",
                    "STAFF-KTT",
                    "KTT-STAFF--MAGANG-",
                    "EKSTERNAL-RELATION-&-SECURITY-HEAD",
                    "SECURITY-SUPERVISOR",
                    "EKSTERNAL-RELATION-&-SECURITY-ADMIN",
                    "SECURITY",
                    "MOTOR-GRADER-OPERATOR",
                    "GENERAL-WORKER-COORDINATOR",
                    "ACCOUNTING-COORDINATOR",
                    "FINANCE-&-COMMERCIAL-COORDINATOR",
                    "EXTERNAL-RELATION-COORDINATOR",
                    "ADMINISTRATION-COORDINATOR",
                    "AKHMAD-GUNADI-ASSISTANT",
                    "MITRA-BARITO-DIRECTOR-",
                    "ROOM-17-PRODUCTION-ADMIN",
                    "WEIGH-BRIDGE-ADMIN",
                    "CHRUSER-FOREMAN",
                    "ROOM-17-UNIT-HEAD",
                    "STOCK-ROOM-FOREMAN",
                    "PRODUCTION-SUPERVISOR",
                    "QUALITY-CONTROL-CREW",
                    "CRUSHER-&-STOCK-ROOM-FOREMAN",
                    "CRUSHER-MAINTENANCE-FOREMAN",
                    "CREW-MAINTENANCE-CRUSHER",
                    "MAINTENANCE-CREW",
                    "ADT-OPERATOR",
                    "PRODUCTION-CHECKER",
                    "CHECKER",
                    "PRODUCTION-FOREMAN",
                    "HD-OPERATOR",
                    "PIT-SERVICE-MAINTENANCE",
                    "PRODUCTION-GROUP-LEADER",
                    "PUMP-OPERATOR",
                    "HRD-OFFICER",
                    "HRD-RELATION",
                    "ENGINEER",
                    "ENGINEERING-ADMIN",
                    "SURVEY-CREW",
                    "SURVEYOR-ASSISTANT",
                    "HAULING-FOREMAN",
                    "MECHANIC",
                    "MECHANIC-HELPER",
                    "JUNIOR-MECHANIC",
                    "SERVICE-MAINTENANCE-FOREMAN",
                    "LOGISTIC-ADMIN",
                    "PLANT-ADMIN",
                    "GENSET-OPERATOR",
                    "WATER-PUMP-OPERATOR",
                    "AUTO-ELECTRICIAN-FOREMAN",
                    "JUNIOR-ELECTRIC",
                    "MECHANIC-FOREMAN",
                    "SENIOR-AUTO-ELECTRICIAN",
                    "FUELMAN",
                    "FUEL-TRUCK-DRIVER",
                    "FUEL-FOREMAN",
                    "FUEL-ADMIN",
                    "LATHE-OPERATOR",
                    "SENIOR-LATHE-OPERATOR",
                    "PURCHASING-ADMIN",
                    "WAREHOUSE-MAN",
                    "MECHANIC-ELECTRICIAN",
                    "MECHANIC-SUPERVISOR",
                    "JR--MECHANIC",
                    "ELECTRICIAN",
                    "PLANT-MANAGER",
                    "PLANNER",
                    "PLANT-&-LOGISTIC-JR--OFFICER",
                    "SENIOR-MECHANIC",
                    "SERVICE-MAINTENANCE",
                    "SENIOR-WELDER",
                    "WELDER",
                    "PLANT-SUPERVISOR",
                    "TYREMAN",
                    "LOGISTIC-OPERATIONAL",
                    "STOREKEEPER",
                    "WELDER-HELPER",
                    "LOGISTIC-SUPERVISOR",
                    "PAINTING-MECHANIC",
                    "TYRE-MAN",
                    "AUTO-ELECTRICIAN",
                    "LOGISTIC-GROUP-LEADER",
                    "MECHANIC-COORDINATOR",
                    "MECHANIC-TRAINEE",
                    "GENERAL-TECHNICIAN",
                    "KTT-JUNIOR-STAFF",
                    "MAINTENANCE-ROAD-FOREMAN",
                    "CHIEF-MECHANIC",
                    "MECHANIC-WELDER",
                    "PAINTING-EQUIPTMENT-SERVICE",
                    "GRADER-OPERATOR",
                    "MASTER-LOADING",
                    "LCT-MAINTENANCE",
                    "PORT-ADMIN",
                    "PORT-DEPARTMENT-HEAD",
                    "PORT-FOREMAN",
                    "VIBRO-COMPACT-OPERATOR",
                    "MAGANG",
                    "DOZER-OPERATOR",
                    "WATER-TRUCK-DRIVER",
                    "HAULING-ADMIN",
                    "DT-DRIVER-",
                    "FOREMAN-HAULING",
                    "HAULING-JR--OFFICER",
                    "HRGA-ADMINISTRATOR",
                    "HRGA-OFFICER",
                    "WAKAR",
                    "LUBE-TRUCK-DRIVER",
                    "SAFETY-CREW",
                    "PJO-SITE-TOP",
                    "JUNIOR-ENGINEER",
                    "HELPER-MEKANIK",
                    "GENERAL-ADMINISTRATOR",
                    "MIS",
                    "HELPER-TECHNICIAN",
                    "ADMIN",
                    "KOORDINATOR-ASSIST",
                    "DIREKTUR-PT-MBLE",
                    "PENGAWAS",
                    "CONTROLLING",
                    "UMUM",
                    "LOGISTIC",
                    "LEGAL"
                ],
                "from" => null,
            ],
            'ON_FILTER' => [
                'day' => date('d'),
                'month' => date('m'),
                'year' => date('Y'),
                "date_start" => "2025-09-01",
                "date_end" => "2025-09-30",
                "statusKaryawan" => [
                    "AKTIVE",
                    "PHK-BULAN-INI"
                ],
                "FEATURE" => [
                    "ABSENSI"
                ],
                "PERUSAHAAN" => [
                    "PT--MBLE",
                    "PT--KPN",
                    "PT--MB",
                    "CV--BK"
                ],
                "PROJECT" => [
                    "MBG",
                    "HAULING",
                    "WORKSHOP",
                    "HAULING-PT-TOP",
                    "IUP-CV--BK",
                    "IUP-PT--MB",
                    "KONTRAKTOR-PT--MB",
                    "PORT",
                    "EXTERNAL",
                    "CRUSHER",
                    "PROJEK-UMUM",
                    "UMUM",
                    "KBU",
                    "TOP",
                    "EX",
                    "PENDANG",
                    "HRGA",
                    "WAYANG",
                    "EKSTERNAL"
                ],
                "DEPARTEMEN" => [
                    "HRGA",
                    "HAULING",
                    "PRODUKSI",
                    "ENGINEERING",
                    "HSE",
                    "PLANT",
                    "PORT",
                    "LOGISTIC",
                    "PLAN",
                    "FINANCE",
                    "MANAJEMEN",
                    "MECHANIC",
                    "CIVIL",
                    "ERS",
                    "FA-&-TAX",
                    "BOD",
                    "MBG",
                    "CRUSHER",
                    "KONTRAKTOR-PT--MB",
                    "UMUM",
                    "PORT---ROOM-17",
                    "WAYANG",
                    "EKSTERNAL"
                ],
                "DIVISI" => [
                    "IT",
                    "HR",
                    "NON",
                    "ENGINEERING",
                    "HAULING",
                    "HRGA",
                    "FA-&-TAX",
                    "GA",
                    "HSE",
                    "MEDIC",
                    "PLANT",
                    "PORT",
                    "SAFETY",
                    "PRODUKSI",
                    "ENVIRO",
                    "MANAJEMEN",
                    "ELECTRICK",
                    "CRUSHER",
                    "MECHANIC",
                    "EKSTERNAL",
                    "FUEL",
                    "ROAD-MAINTENACE",
                    "WELDER",
                    "LATHE",
                    "SPARE-PART",
                    "SERVICE-MAINTENANCE",
                    "PLAN",
                    "SUPPORT",
                    "SURVEY",
                    "TYRE",
                    "EKTERNAL",
                    "SAWIT",
                    "SECURITY",
                    "MBG",
                    "EXTERNAL",
                    "KONTRAKTOR-PT--MB",
                    "PROJEK-UMUM",
                    "UMUM",
                    "WAYANG"
                ],
                "KARYAWAN" => [
                    "MBLE-0422003",
                    "MBLE-240102",
                    "MBLE-240473",
                    "MBLE-250211",
                    "MBLE-210508",
                    "MB-PL-100012",
                    "MBLE-210496",
                    "MB-HO-200090",
                    "MB-HO-210092",
                    "MBLE-0219070054",
                    "MBLE-240127",
                    "MB-HO-140036",
                    "MB-F01-150069",
                    "MBLE-2411139",
                    "MBLE-240115",
                    "MBLE-230921",
                    "MB-250106",
                    "MB-F01-200255",
                    "MB-F01-150065",
                    "MB-PL-250106",
                    "MBLE-0422027",
                    "MBLE-220591",
                    "MBLE-200369",
                    "MBLE-05230385",
                    "MB-F01-190172",
                    "MBLE-2408103",
                    "MB-PL-2411137",
                    "MB-PL-2411138",
                    "MB-PL-250103",
                    "MBLE-250487",
                    "MBLE-250332",
                    "MBLE-2509230",
                    "MBLE-240116",
                    "MBLE-220822",
                    "MBLE-110027",
                    "MBLE-220528",
                    "MBLE-110031",
                    "MBLE-0321100002",
                    "MB-F01-130045",
                    "MB-F01-170109",
                    "MB-F01-180153",
                    "MBLE-230920",
                    "MB-F01-180157",
                    "MBLE-230926",
                    "MBLE-240117",
                    "MBLE-240126",
                    "MBLE-250435",
                    "MBLE-2505137",
                    "MBLE-2507186",
                    "MBLE-2508203",
                    "MBLE-231001",
                    "MBLE-0422081",
                    "MBLE-2409111",
                    "MBLE-2412141",
                    "MBLE-2505130",
                    "MBLE-2508202",
                    "MBLE-230852",
                    "MBLE-250324",
                    "MBLE-250327",
                    "MB-2505125",
                    "MBLE-2505126",
                    "MBLE-2505127",
                    "MBLE-2508190",
                    "MBLE-2509221",
                    "MBLE-080006",
                    "MBLE-230929",
                    "MBLE-240104",
                    "MBLE-230910",
                    "MBLE-062302010",
                    "MBLE-240791",
                    "MBLE-2504107",
                    "MB-MG-2508201",
                    "MBLE-230871",
                    10924,
                    150924,
                    230924,
                    "MBLE-231204",
                    "MB-PL---2409117",
                    "MBLE-220608",
                    "MB-PL-170232",
                    "MB-HO-180052",
                    "MBLE-231110",
                    "MBLE-0218120001",
                    "MBLE-200375",
                    "MBLE-120020",
                    "MB-FO-170342",
                    "MB-F0-170342",
                    "MBLE-220535",
                    "MBLE-220718",
                    "MB-F01-130037",
                    "MB-F01-160099",
                    "MBLE-110021",
                    "MB-PL-2410123",
                    "MB-PL-250105",
                    "MB-PL-250481",
                    "MBLE-2411132",
                    "MBLE-0422083",
                    "BK-PL-220367",
                    "MB-PL-240796",
                    "MB-PL-250101",
                    "MB-PL-2509206",
                    "MBLE-230845",
                    "MB-250481",
                    "MBLE-2504112",
                    "MBLE-0422001",
                    "MB-PL-2410129",
                    "MB-HO-100014",
                    "MBLE-250436",
                    "MBLE-250329",
                    "MBLE-250484",
                    "MBLE-250437",
                    "MBLE-250439",
                    "MBLE-250440",
                    "MBLE-250442",
                    "MBLE-250445",
                    "MBLE-250447",
                    "MBLE-250448",
                    "MBLE-250449",
                    "MBLE-250451",
                    "MBLE-250452",
                    "MBLE-250453",
                    "MBLE-250456",
                    "MBLE-250459",
                    "MBLE-250460",
                    "MBLE-250461",
                    "MBLE-250463",
                    "MBLE-250464",
                    "MBLE-250465",
                    "MBLE-250466",
                    "MBLE-250468",
                    "MBLE-250469",
                    "MBLE-250470",
                    "MBLE-250403",
                    "MBLE-250471",
                    "MBLE-250472",
                    "MBLE-250473",
                    "MBLE-250474",
                    "MBLE-250475",
                    "MBLE-2504106",
                    "MBLE-2508192",
                    "MBLE-2508198",
                    "MBLE-2508204",
                    "MBLE-2508205",
                    "MBLE-2509210",
                    "MBLE-2509211",
                    "MBLE-2509212",
                    "MBLE-2509231",
                    "MBLE-2509232",
                    "MBLE-2509233",
                    "MBLE-2509234",
                    "MB-PL-2509220",
                    "MB-PL-2509227",
                    "MBLE-2507154",
                    "MBLE-2507155",
                    "MBLE-2507157",
                    "MBLE-2507162",
                    "MBLE-2507163",
                    "MBLE-2507182",
                    "MBLE-2507183",
                    "MB-HO-050007",
                    "MB-HO-040006",
                    "MB-HO-180051",
                    "MBLE-0422002",
                    "MB-HO-130032",
                    "MB-HO-130028",
                    "MB-HO-200063",
                    "MBLE-240132",
                    "MBLE-140143",
                    "MBLE-240359",
                    "MBLE-2504101",
                    "MBLE-220595",
                    "MBLE-210431",
                    "MBLE-220596",
                    "MB-PL-120046",
                    "MBLE-250488",
                    "MBLE-220761",
                    "MBLE-240240",
                    "MBLE-240239",
                    "MBLE-062304024",
                    "MBLE-2411130",
                    "MBLE-2411131",
                    "MBLE-2411133",
                    "MBLE-2411134",
                    "MB-MG-2507174",
                    "MB-MG-2507177",
                    "MB-PL-220524",
                    "MBLE-210416",
                    "MB-F01-160093",
                    "MB-PL-2509208",
                    "MBLE-250490",
                    "MBLE-2504109",
                    "MBLE-2504104",
                    "MBLE-2507160",
                    "MBLE-2505119",
                    "MB-MG-2507172",
                    "MB-MG-2507173",
                    "MBLE-0219010015",
                    "MBLE-210517",
                    "MBLE-220769",
                    "MBLE-220678",
                    "MBLE-220660",
                    "MBLE-220659",
                    "MBLE-240246",
                    "MBLE-2408106",
                    "MBLE-2411140",
                    "MBLE-220758",
                    "MBLE-220533",
                    "MB-F01-200221",
                    "MBLE-220669",
                    "MBLE-05230168",
                    "MBLE-231206",
                    "MBLE-240133",
                    "MBLE-240256",
                    "MBLE-250217",
                    "MBLE-0422049",
                    "MBLE-0422051",
                    "MB-PL-2509207",
                    "MBLE-220540",
                    "MBLE-220754",
                    "MBLE-130069",
                    "MBLE-220642",
                    "MBLE-220604",
                    "MBLE-210447",
                    "MBLE-220531",
                    "MBLE-170227",
                    "BK-PL-170184",
                    "MBLE-220727",
                    "MB-PL-210420",
                    "MBLE-05230281",
                    "MBLE-220639",
                    "MBLE-220594",
                    "MBLE-220719",
                    "MBLE-052212047",
                    "MBLE-241227",
                    "MB-F01-200236",
                    "MBLE-230858",
                    "MBLE-2409113",
                    "MBLE-190328",
                    "MB-F0-150147",
                    "BK-PL-120059",
                    "MBLE-220681",
                    "MBLE-0219110098",
                    "MB-F01-170121",
                    "MBLE-210430",
                    "MBLE-210471",
                    "MBLE-220729",
                    "MBLE-210482",
                    "MBLE-210429",
                    "MBLE-0219100080",
                    "MB-F01-150075",
                    "MBLE-0422030",
                    "MB-PL-250214",
                    "MBLE-220616",
                    "MBLE-250218",
                    "MBLE-2507158",
                    "MBLE-2507159",
                    "MBLE-170239",
                    "MBLE-220686",
                    "MBLE-220607",
                    "MBLE-210428",
                    "MBLE-200382",
                    "MBLE-240583",
                    "MBLE-240585",
                    "MBLE-080005",
                    "MBLE-210437",
                    "MBLE-05230284",
                    "MBLE-052211020",
                    "MBLE-220530",
                    "MBLE-190343",
                    "MBLE-0322010063",
                    "MBLE-0422010",
                    "MBLE-220711",
                    "MBLE-220775",
                    "MBLE-230867",
                    "MB-F01-160101",
                    "MBLE-062307028",
                    "MBLE-0422042",
                    "MBLE-250215",
                    "MB-F01-190179",
                    "MBLE-0219120106",
                    "MBLE-170247",
                    "MBLE-220776",
                    "MB-F01-110023",
                    "MBLE-2505131",
                    "MBLE-231201",
                    "MBLE-220560",
                    "MBLE-130100",
                    "MBLE-140138",
                    "MB-F01-150087",
                    "MBLE-0220010109",
                    "MB-HO-190062",
                    "MB-HO-080010",
                    "MB-HO-100015",
                    "MBLE-210391",
                    "MBLE-231006",
                    "MBLE-140123",
                    "MB-F01-150072",
                    "MBLE-120042",
                    "MBLE-160163",
                    "MBLE-220691",
                    "MBLE-220707",
                    "MBLE-220791",
                    "MBLE-230881",
                    "MBLE-210390",
                    "MBLE-0321100010",
                    "MBLE-240581",
                    "MBLE-240797",
                    "MBLE-250325",
                    "MBLE-0422022",
                    "MB-PL-250212",
                    "MBLE-250326",
                    "MBLE-0422005",
                    "MBLE-100014",
                    "MBLE-0321110039",
                    "MBLE-2409112",
                    "MBLE-2409114",
                    "MBLE-2505143",
                    "MBLE-110024",
                    "MBLE-220697",
                    "MBLE-220638",
                    "MBLE-230918",
                    "MBLE-2410127",
                    "MB-MG-2410128",
                    "MBLE-130073",
                    "MBLE-200374",
                    "MBLE-220647",
                    "MBLE-220835",
                    "MBLE-130066",
                    "MB-PL-250110",
                    "MBLE-120056",
                    "MBLE-220703",
                    "MB-F01-200225",
                    "MBLE-200378",
                    "MBLE-240105",
                    "MBLE-240122",
                    "MBLE-240123",
                    "MB-F01-180156",
                    "MBLE-240793",
                    "MBLE-240798",
                    "MBLE-2410120",
                    "MBLE-2410121",
                    "MBLE-2410122",
                    "MBLE-2410126",
                    "MBLE-250483",
                    "MBLE-250485",
                    "MBLE-250486",
                    "MBLE-2507184",
                    "MBLE-2507185",
                    "MB-PL-2410128",
                    "MBLE-2509218",
                    "MBLE-2509219",
                    "MBLE-2507164",
                    "MBLE-2507165",
                    "MBLE-2507166",
                    "MBLE-2507181",
                    "MBLE-2507168",
                    "MB-MG-2507175",
                    "MB-MG-2507176",
                    "MBLE-2507178",
                    "MBLE-2507179",
                    "MBLE-2509214",
                    "MBLE-2509215",
                    "MBLE-2507188",
                    "MBLE-2507189",
                    "MBLE-2508199",
                    "MBLE-2508200",
                    "MBLE-2509222",
                    "MBLE-080007",
                    "MBLE-231003",
                    "MBLE-2504105",
                    "MBLE-2504108",
                    "MBLE-2509209",
                    "MBLE-2509228",
                    "MBLE-2505144",
                    "MB-F01-120034",
                    "MBLE-231002",
                    "MB-PL-130096",
                    "MBLE-0322010050",
                    "MBLE-0322010057",
                    "MB-F01-100014",
                    "MB-F01-080007",
                    "MB-HO-230106",
                    "MB-F01-150058",
                    "MBET-2111010013",
                    "MBLE-240564",
                    "MBLE-170238",
                    "MBLE-210422",
                    "MBLE-210458",
                    "MBLE-200379",
                    "MBLE-130112",
                    "MBLE-210436",
                    "MBLE-240258",
                    "MB-PL-200364",
                    "MB-PL-220527",
                    "MB-PL-220526",
                    "MBLE-240101",
                    "MBLE-170220",
                    "MB-F01-150056",
                    "MB-PL-220829",
                    "MB-PL-120055",
                    "MB-PL-200383",
                    "MBLE-130088",
                    "MB-PL-220818",
                    "MB-PL-240687",
                    "MBLE-240248",
                    "MBLE-190315",
                    "MB-F01-100015",
                    "MB-PL-2409117",
                    "MBLE-2505139",
                    "MBLE-2505140",
                    "MBLE-190340",
                    "MB-PL-230883",
                    "MB-PL-220823",
                    "MB-PL-220826",
                    "MB-PL-220824",
                    "MB-PL-210386",
                    "MB-PL-110035",
                    "MB-PL-170209",
                    "MB-PL-210385",
                    "MB-PL-210387",
                    "MBLE-2505123",
                    "MBLE-2505124",
                    "MB-PL-120054",
                    "MB-PL-100009",
                    "MBLE-230880",
                    "MBLE-220721",
                    "MB-PL-100013",
                    "MBLE-0321100022",
                    "MBLE-2505146",
                    "MBLE-2505132",
                    "MB-PL-210468",
                    "MBLE-220644",
                    "MBLE-190333",
                    "MBLE-180285",
                    "MB-PL-240688",
                    "MBLE-2505122",
                    "MBLE-2505133",
                    "MBLE-2505134",
                    "MBLE-2505145",
                    "MBLE-2507153",
                    "MBLE-052211002",
                    "MB-PL-210388",
                    "MB-PL-200368",
                    "MB-250108",
                    "MB-250109",
                    "MB-PL-250108",
                    "MB-PL-250109",
                    "MBLE-250479",
                    "MBLE-250480",
                    "MBLE-250491",
                    "MBLE-250492",
                    "MBLE-250493",
                    "MBLE-250494",
                    "MBLE-250495",
                    "MBLE-250496",
                    "MBLE-250499",
                    "MBLE-2504100",
                    "MBLE-2504111",
                    "MBLE-250330",
                    "MBLE-250331",
                    "MBLE-2504115",
                    "MBLE-2505117",
                    "MBLE-2505129",
                    "MBLE-2505135",
                    "MBLE-2505138",
                    "MBLE-2505142",
                    "MBLE-2507180",
                    "MBLE-2508191",
                    "MBLE-2509213",
                    "MBLE-2509223",
                    "MBLE-2509224",
                    "MBLE-2509225",
                    "MBLE-2509226",
                    "MB-PL-2509229",
                    "MBLE-2506151",
                    "MBLE-2509216",
                    "MBLE-2506150",
                    "MB-MG-2507169",
                    "MBLE-2508193",
                    "MBLE-2508194",
                    "MBLE-2508195",
                    "MBLE-2509217",
                    "MB-F01-150080",
                    "MBLE-170185",
                    "MBLE-130090",
                    "MBLE-130082",
                    "MBLE-230898",
                    "MBLE-220551",
                    "MBLE-220613",
                    "MBLE-220609",
                    "MBLE-130101",
                    "MBLE-140130",
                    "MBLE-210394",
                    "MBLE-130075",
                    "MBLE-130092",
                    "MBLE-130074",
                    "MBLE-140119",
                    "MBLE-0321110040",
                    "MBLE-240106",
                    "MBLE-240110",
                    "MBLE-240113",
                    "MBLE-240362",
                    "MBLE-210493",
                    "MBLE-240579",
                    "MBLE-240580",
                    "MBLE-2407101",
                    "MBLE-2409108",
                    "MBLE-2409109",
                    "MBLE-2409110",
                    "MBLE-0422061",
                    "MBLE-032110028",
                    "MBLE-250220",
                    "MBLE-2505141",
                    "MBLE-062307025",
                    "MBLE-052212052",
                    "MBLE-05230174",
                    "MBLE-062307026",
                    "MBLE-240253",
                    "MBLE-052212049",
                    "W-6",
                    "MBLE-052211037",
                    "MBLE-220629",
                    "MBLE-05230171",
                    "MBLE-0422062",
                    "MBLE-210426",
                    "MBLE-231005",
                    "MBLE-0220020110",
                    "MBLE-0322010058",
                    "MBLE-062304018",
                    "MBLE-2409116",
                    "MBLE-05230170",
                    "MB-F01-150067",
                    "MBLE-230934",
                    "MBLE-052211011",
                    "MBLE-240251",
                    "MBLE-062302011",
                    "MBLE-220699",
                    "MBLE-220614",
                    "MBLE---2410118",
                    "MBLE-2507187",
                    "MBLE-052212065",
                    "MBLE-2410118",
                    "MBLE-0220010107",
                    "MBLE-05230173",
                    "MBLE-0422069",
                    "MBLE-062307030",
                    "MBLE-05230172",
                    "MBLE-0321120046",
                    "MBLE-230847",
                    "MBLE-230914",
                    "MBLE-2411135",
                    "MB-F01-110022",
                    "MBLE-170205",
                    "MBLE-230906",
                    "MBLE-170233",
                    "MBLE-140131",
                    "MBLE-170249",
                    "MBLE-210396",
                    "MBLE-130086",
                    "MBLE-130102",
                    "MBLE-240109",
                    "MBLE-240111",
                    "MBLE-220538",
                    "BK-PL-130108",
                    "MBLE-210435",
                    290824,
                    "MB-PL-290824",
                    190924,
                    "MB-PL-190924",
                    "MBLE-250221",
                    "MBLE-2507161",
                    "MB-MG-2507170",
                    "MB-MG-2507171",
                    "MBLE-210413",
                    "MBLE-0422082",
                    "MB-HO-210094",
                    "MB-F02-060002",
                    "MB-F02-050001",
                    "MB-F02-150089",
                    "MB-F02-150084",
                    "MB-F02-170111",
                    "MB-F02-180142",
                    "MB-F02-200205",
                    "MB-F02-180145",
                    "MB-F02-180146",
                    "MB-F02-190193",
                    "MB-F02-190159",
                    "MB-F02-190167",
                    "MB-F02-200206",
                    "MB-F02-220213",
                    "MB-F02-170120",
                    "MB-F02-230915",
                    "MB-F02-230916",
                    "MB-F02-220217",
                    "MB-F02-200204",
                    "MB-F02-200201",
                    "MB-DKI-010001",
                    "MBLE-010001",
                    "MB-HO-200089",
                    "MB-HO-200088",
                    "005",
                    "006",
                    "007",
                    "008",
                    "MB-CN-010004",
                    "MB-HO-010001",
                    "012",
                    "013",
                    "014",
                    "015",
                    "MB-HO-210314"
                ],
                "JABATAN" => [
                    "SENIOR-OFFICER-MIS",
                    "CIVIL-CREW",
                    "CIVIL-MAINTENANCE-COORDINATOR",
                    "LV-DRIVER",
                    "GENERAL-AFFAIR-GROUP-LEADER",
                    "GARDEN-KEEPER",
                    "GENERAL-AFFAIR-OFFICER",
                    "GENERAL-WORKER",
                    "GENERAL-AFFAIR-JR--OFFICER",
                    "KOKI",
                    "LAUNDRY",
                    "USTADZ",
                    "JUNIOR-WELDER",
                    "TEHNICIAN",
                    "GARDEN-WORKER",
                    "PLUMBING-TECHNICIAN",
                    "BUS-DRIVER",
                    "CR-&-LA-PERSONNEL",
                    "HRD-SUPERINTENDENT",
                    "PAYROLL-CLERK",
                    "PAYROLL-OFFICER",
                    "TRAINEER",
                    "HUMAN-RESOURCES-EKSTERNAL-RELATION",
                    "HRD-ADMIN",
                    "HRD-JR--OFFICER",
                    "HR-ADMIN",
                    "INDUSTRIAL-RELATION-SPECIALIST",
                    "PEGAWAI-HARIAN",
                    "ABK",
                    "BULLDOZER-OPERATOR",
                    "CRUSHER-FOREMAN",
                    "DT-DRIVER",
                    "DUMP-MAN",
                    "EXCAVATOR-OPERATOR",
                    "JURAGAN-MB-017",
                    "PORT-CREW",
                    "WASHING-MAN",
                    "WHEEL-LOADER-OPERATOR",
                    "CRUSHER-CREW",
                    "MAINTENANCE-CRUSHER",
                    "FUEL-MONITORING-CONTROL",
                    "TUGBOAT-MOTORIS",
                    "ACCOUNTING-&-TAX",
                    "FINANCE-SENIOR-OFFICER",
                    "OFFICE-SITE-MANAGER",
                    "KEPALA-TEKNIK-TAMBANG",
                    "PROJECT-MANAGER",
                    "GENERAL-MANAGER",
                    "FINANCE-ADMIN",
                    "ENVIRO-CREW",
                    "ENVIROMENT",
                    "NURSERY",
                    "NURSERY-SUPERVISOR",
                    "JUNIOR-ENVIRONMENT",
                    "ENVIRONMENT-&-RECLAMATION-GROUP-LEADER",
                    "HSE-ADMIN",
                    "SAFETY-MAN",
                    "SAFETY-ADMIN",
                    "SAFETY-OFFICER",
                    "HSE-SUPERVISOR",
                    "PARAMEDIC",
                    "JUNIOR-PARAMEDIC",
                    "SAFETY-JR--OFFICER",
                    "MONITORING-CONTROL-JR--OFFICER",
                    "ENGINEER-JR--MINE-PLAN",
                    "MONITORING-CONTROL",
                    "STAFF-KTT",
                    "KTT-STAFF--MAGANG-",
                    "EKSTERNAL-RELATION-&-SECURITY-HEAD",
                    "SECURITY-SUPERVISOR",
                    "EKSTERNAL-RELATION-&-SECURITY-ADMIN",
                    "SECURITY",
                    "MOTOR-GRADER-OPERATOR",
                    "GENERAL-WORKER-COORDINATOR",
                    "ACCOUNTING-COORDINATOR",
                    "FINANCE-&-COMMERCIAL-COORDINATOR",
                    "EXTERNAL-RELATION-COORDINATOR",
                    "ADMINISTRATION-COORDINATOR",
                    "AKHMAD-GUNADI-ASSISTANT",
                    "MITRA-BARITO-DIRECTOR-",
                    "ROOM-17-PRODUCTION-ADMIN",
                    "WEIGH-BRIDGE-ADMIN",
                    "CHRUSER-FOREMAN",
                    "ROOM-17-UNIT-HEAD",
                    "STOCK-ROOM-FOREMAN",
                    "PRODUCTION-SUPERVISOR",
                    "QUALITY-CONTROL-CREW",
                    "CRUSHER-&-STOCK-ROOM-FOREMAN",
                    "CRUSHER-MAINTENANCE-FOREMAN",
                    "CREW-MAINTENANCE-CRUSHER",
                    "MAINTENANCE-CREW",
                    "ADT-OPERATOR",
                    "PRODUCTION-CHECKER",
                    "CHECKER",
                    "PRODUCTION-FOREMAN",
                    "HD-OPERATOR",
                    "PIT-SERVICE-MAINTENANCE",
                    "PRODUCTION-GROUP-LEADER",
                    "PUMP-OPERATOR",
                    "HRD-OFFICER",
                    "HRD-RELATION",
                    "ENGINEER",
                    "ENGINEERING-ADMIN",
                    "SURVEY-CREW",
                    "SURVEYOR-ASSISTANT",
                    "HAULING-FOREMAN",
                    "MECHANIC",
                    "MECHANIC-HELPER",
                    "JUNIOR-MECHANIC",
                    "SERVICE-MAINTENANCE-FOREMAN",
                    "LOGISTIC-ADMIN",
                    "PLANT-ADMIN",
                    "GENSET-OPERATOR",
                    "WATER-PUMP-OPERATOR",
                    "AUTO-ELECTRICIAN-FOREMAN",
                    "JUNIOR-ELECTRIC",
                    "MECHANIC-FOREMAN",
                    "SENIOR-AUTO-ELECTRICIAN",
                    "FUELMAN",
                    "FUEL-TRUCK-DRIVER",
                    "FUEL-FOREMAN",
                    "FUEL-ADMIN",
                    "LATHE-OPERATOR",
                    "SENIOR-LATHE-OPERATOR",
                    "PURCHASING-ADMIN",
                    "WAREHOUSE-MAN",
                    "MECHANIC-ELECTRICIAN",
                    "MECHANIC-SUPERVISOR",
                    "JR--MECHANIC",
                    "ELECTRICIAN",
                    "PLANT-MANAGER",
                    "PLANNER",
                    "PLANT-&-LOGISTIC-JR--OFFICER",
                    "SENIOR-MECHANIC",
                    "SERVICE-MAINTENANCE",
                    "SENIOR-WELDER",
                    "WELDER",
                    "PLANT-SUPERVISOR",
                    "TYREMAN",
                    "LOGISTIC-OPERATIONAL",
                    "STOREKEEPER",
                    "WELDER-HELPER",
                    "LOGISTIC-SUPERVISOR",
                    "PAINTING-MECHANIC",
                    "TYRE-MAN",
                    "AUTO-ELECTRICIAN",
                    "LOGISTIC-GROUP-LEADER",
                    "MECHANIC-COORDINATOR",
                    "MECHANIC-TRAINEE",
                    "GENERAL-TECHNICIAN",
                    "KTT-JUNIOR-STAFF",
                    "MAINTENANCE-ROAD-FOREMAN",
                    "CHIEF-MECHANIC",
                    "MECHANIC-WELDER",
                    "PAINTING-EQUIPTMENT-SERVICE",
                    "GRADER-OPERATOR",
                    "MASTER-LOADING",
                    "LCT-MAINTENANCE",
                    "PORT-ADMIN",
                    "PORT-DEPARTMENT-HEAD",
                    "PORT-FOREMAN",
                    "VIBRO-COMPACT-OPERATOR",
                    "MAGANG",
                    "DOZER-OPERATOR",
                    "WATER-TRUCK-DRIVER",
                    "HAULING-ADMIN",
                    "DT-DRIVER-",
                    "FOREMAN-HAULING",
                    "HAULING-JR--OFFICER",
                    "HRGA-ADMINISTRATOR",
                    "HRGA-OFFICER",
                    "WAKAR",
                    "LUBE-TRUCK-DRIVER",
                    "SAFETY-CREW",
                    "PJO-SITE-TOP",
                    "JUNIOR-ENGINEER",
                    "HELPER-MEKANIK",
                    "GENERAL-ADMINISTRATOR",
                    "MIS",
                    "HELPER-TECHNICIAN",
                    "ADMIN",
                    "KOORDINATOR-ASSIST",
                    "DIREKTUR-PT-MBLE",
                    "PENGAWAS",
                    "CONTROLLING",
                    "UMUM",
                    "LOGISTIC",
                    "LEGAL"
                ],
                "from" => null,
            ],
            'USER' => $users
        ];



        $Q_data = DB::table('database_fields')
            ->join('database_data', function ($join) {
                $join->on('database_fields.code_field', '=', 'database_data.code_field_data')
                    ->on('database_fields.code_table_field', '=', 'database_data.code_table_data');
            })
            ->join('database_tables', function ($join) {
                $join->on('database_data.code_table_data', '=', 'database_tables.code_table')
                    ->on('database_data.code_table_data', '=', 'database_tables.code_table');
            })
            ->where('database_fields.level_data_field', '<=', $users->role)
            ->select(['database_data.*', 'database_fields.level_data_field', 'database_tables.menu_table'])
            ->get();

        $Q_data_single = DB::table('database_fields')
            ->join('database_data', function ($join) {
                $join->on('database_fields.code_field', '=', 'database_data.code_field_data')
                    ->on('database_fields.code_table_field', '=', 'database_data.code_table_data');
            })
            ->join('database_tables', function ($join) {
                $join->on('database_data.code_table_data', '=', 'database_tables.code_table')
                    ->on('database_data.code_table_data', '=', 'database_tables.code_table');
            })
            ->where('database_data.code_data', '<=', ResponseFormatter::toUUID($users->nrp))
            ->select(['database_data.*', 'database_fields.level_data_field', 'database_tables.menu_table'])
            ->get();

        // join $Q_data &  $Q_data_self
        $Q_data_self = $Q_data->merge($Q_data_single);




        $database_data = [];
        foreach ($Q_data_self as $data) {
            $value_data = [
                'value_data' => $data->value_data,
                'uuid_data' => $data->uuid_data,
                'text_data' => $data->value_data,
                'code_data' => $data->code_data,
                'id'    => $data->id
            ];
            $database_data[$data->code_table_data][$data->code_data][$data->code_field_data] = $value_data;
            // $database_data[$data->level_data_field][$data->menu_table][$data->code_table_data][$data->code_data][$data->code_field_data] = $value_data;
        }

        $Q_data_DatabaseFieldShow = DatabaseFieldShow::get();
        $dataDatabaseFieldShow = [];
        foreach ($Q_data_DatabaseFieldShow as $data_user_template) {
            $dataDatabaseFieldShow[$data_user_template->table_code][$data_user_template->field_code][$data_user_template->sort_field] = $data_user_template;
        }



        // Get Database Group Forms
        $Q_group_forms = GroupForm::get();
        $data_group_forms = [];
        foreach ($Q_group_forms as $group_forms) {
            $data_group_forms[$group_forms->uuid] = $group_forms;
        }

        $Q_data_DatabaseFieldShow = DatabaseFieldShow::get();
        $dataDatabaseFieldShow = [];
        foreach ($Q_data_DatabaseFieldShow as $data_user_template) {
            $dataDatabaseFieldShow[$data_user_template->table_code][$data_user_template->field_code][$data_user_template->sort_field] = $data_user_template;
        }

        $Q_data_source = DatabaseDataSource::get(['field_get_data_source', 'code_data_source', 'table_data_source']);
        $data_data_source = [];
        foreach ($Q_data_source as $data_source) {
            $data_data_source[$data_source->code_data_source] = $data_source;
        }

        // Get Database Tables
        $Q_table = DatabaseTable::get([
            'code_table',
            'parent_table',
            'primary_table',
            'menu_table',
            'description_table'
        ]);
        $data_table = [];
        $data_table_child = [];
        $data_table_menu = [];

        // Get Database Fields
        $Q_field = DatabaseField::get([
            'code_table_field',
            'description_field',
            'type_data_field',
            'level_data_field',
            'code_field',
            'visibility_data_field',
            'full_code_field',
            'sort_field'
        ]);
        $data_field = [];
        $data_field_join = [];
        foreach ($Q_field as $field) {
            if (isset($data_data_source[$field->full_code_field])) {
                $field->data_source = $data_data_source[$field->full_code_field];
            }
            $data_field[$field->code_table_field][$field->code_field] = $field;
        }

        foreach ($Q_table as $table) {
            $data_table[$table->code_table] = $table;

            if (isset($data_field[$table->code_table])) {
                $data_table[$table->code_table]['fields'] = $data_field[$table->code_table];
            }
            if (isset($database_data[$table->code_table])) {
                $data_table[$table->code_table]['data'] = $database_data[$table->code_table];
            }

            $data_table[$table->code_table] = $table;
            $data_table_menu[$table->menu_table][] = $table->code_table;

            if ($table->parent_table) {
                $data_table_child[$table->parent_table][] = $table->code_table;
            }
        }

        if (!empty($data_field_join)) {
            foreach ($data_field_join as $code_table_parent => $field_join) {
                if (isset($data_field[$code_table_parent])) {
                    $data_field[$code_table_parent] = array_merge($data_field[$code_table_parent], $field_join['join_fields']);
                } else {
                    $data_field[$code_table_parent] = $field_join['join_fields'];
                }
            }
        }

        foreach ($data_table_child as $parent_table => $child_tables) {

            if (isset($data_field[$parent_table])) {

                $data_table[$parent_table]['join_fields'] = [];

                foreach ($child_tables as $child_table) {

                    if (isset($data_field[$child_table])) {

                        // copy langsung tanpa spread agar associative key tetap utuh
                        $field_childs = $data_field[$child_table];

                        // merge associative array tanpa menghilangkan key
                        $data_table[$parent_table]['join_fields'] += $field_childs;
                    }
                }
            }
        }

        // ====== MANIPULATION DATA ===================

        $data_table_new = json_decode(json_encode($data_table), true);

        foreach ($data_table as $table_code => $tables) {

            foreach ($tables['fields'] as $field_code => $field) {

                if ($field['type_data_field'] == 'DARI-TABEL') {
                    // echo $field_code;
                    // echo $table_code.'~'.$field_code. PHP_EOL;
                    // dd($tables);
                    $data_source = $field['data_source'];
                    $table_src = $data_source['table_data_source'];      // PERUSAHAAN
                    $field_src = $data_source['field_get_data_source'];  // NAMA-PERUSAHAAN-PENDEK

                    // pastikan tabel sumber ada
                    if (!isset($data_table[$table_src]['data'])) continue;
                    if (!isset($tables['data'])) continue;
                    // looping data
                    foreach ($tables['data'] as $row_code => $row) {

                        // ambil value_data untuk mencari di table source

                        if (!isset($row[$field_code]['value_data'])) {
                            continue;
                        }
                        $key_lookup = $row[$field_code]['value_data']; // contoh: "CV--MB"
                        // cek apakah key lookup ada di table source
                        if (!isset($data_table[$table_src]['data'][$key_lookup])) {
                            continue;
                        }

                        // ambil text_data dari tabel sumber
                        $new_text = $data_table[$table_src]['data'][$key_lookup][$field_src]['text_data'] ?? null;

                        if ($new_text !== null) {
                            $data_table_new[$table_code]['data'][$row_code][$field_code]['text_data'] = $new_text;
                        }
                    }
                }
            }
        }

        $data_table = $data_table_new;
        foreach ($data_table_child as $parent_table => $child_tables) {

            // Jika parent tidak punya data → skip agar tidak error
            if (empty($data_table_new[$parent_table]['data']) || !is_array($data_table_new[$parent_table]['data'])) {
                $data_table_new[$parent_table]['join_data'] = [];
                continue;
            }

            $joined_data = [];

            // Loop data parent
            foreach ($data_table_new[$parent_table]['data']  as $key_parent_data => $value_parent_data) {

                // Inisiasi data parent
                $dataParent = is_array($value_parent_data) ? $value_parent_data : [];

                // Loop child table
                foreach ($child_tables as $child_table) {

                    // Jika child tidak ada → lewati
                    if (empty($data_table_new[$child_table]['data']) || !is_array($data_table_new[$child_table]['data'])) {
                        continue;
                    }

                    // Ambil data child berdasarkan key parent
                    $data_table_child_value = $data_table_new[$child_table]['data'][$key_parent_data] ?? null;

                    // Jika tidak ada data child → skip
                    if (!empty($data_table_child_value) && is_array($data_table_child_value)) {
                        $dataParent = array_merge($dataParent, $data_table_child_value);
                    }
                }

                // Simpan hasil join per parent key
                $joined_data[$key_parent_data] = $dataParent;
            }

            // Simpan hasil join ke data_table
            $data_table_new[$parent_table]['join_data'] = $joined_data;
        }
        // dd($data_table_new['KARYAWAN']);

        // ====== APPLY USER TEMPLATE ===================
        $Q_table_field_show = UserTemplate::where('employee_uuid', ResponseFormatter::toUUID($users->nrp))->get();

        $data_table_field_show = [];

        // Loop hasil query
        foreach ($Q_table_field_show as $table) {

            // Cek table code ada di $data_table
            if (!empty($table->code_table) && isset($data_table_new[$table->code_table])) {

                // Pastikan key array terbentuk
                if (!isset($data_table_field_show[$table->code_table]['field_show'])) {
                    $data_table_field_show[$table->code_table]['field_show'] = [];
                }

                // Tambahkan field_show hanya jika ada datanya
                if (!empty($table->code_field)) {
                    $data_table_field_show[$table->code_table]['field_show'][] = $table->code_field;
                }
            }
        }

        // Proses apply ke $data_table
        foreach ($data_table_field_show as $code_table => $table) {

            // Pastikan $data_table ada key tsb dan field_show tidak kosong
            if (isset($data_table[$code_table]) && !empty($table['field_show'])) {
                $data_table_new[$code_table]['field_show'] = $table['field_show'];
            }
        }

        // ========================================================

        // ====== MANIPULATION DATA ===================

        foreach ($dataDatabaseFieldShow as $key_table => $gabungan_fields) {
            # code...
            if (isset($data_table_new[$key_table]['data'])) {
                $table_data = isset($data_table_new[$key_table]['join_data']) ? $data_table_new[$key_table]['join_data'] : $data_table_new[$key_table]['data'];
                // return $gabungan_fields;
                $isJoin = isset($data_table_new[$key_table]['join_data']) ? true : false;
                foreach ($table_data as $code_data => $data_value) { // looping data

                    foreach ($gabungan_fields as $field_fields) { // looping field gabungan
                        $val_gabungan = '';
                        foreach ($field_fields as $field_value) {
                            $val_gabungan = $val_gabungan . $data_value[$field_value['field_show_code']]['text_data'] . $field_value['split_by'];
                        }
                        $arr_data = [
                            'value_data' => rtrim($val_gabungan, $field_value['split_by']),
                            'uuid_data' => $data_value[$field_value['field_show_code']]['uuid_data'],
                            'text_data' => rtrim($val_gabungan, $field_value['split_by']),
                            'code_data' => $code_data,
                            'id'    => null
                        ];

                        if ($isJoin) {
                            $data_table_new[$key_table]['join_data'][$code_data][$field_value['field_code']] = $arr_data;
                        }
                        $data_table_new[$key_table]['data'][$code_data][$field_value['field_code']] = $arr_data;
                    }
                    // return $val_gabungan;
                }
            }
        }




        $default_database = [

            'database_tables' => $data_table_new,
            'data_group_forms' => $data_group_forms,
            'database_tables_child' => $data_table_child,
            'database_data_source' => $data_data_source,
            'database_tables_menu' => $data_table_menu,
            'database_field_show' => $dataDatabaseFieldShow
        ];

        if ($users->role >= 11) {

            $default_database['database_data'] = $database_data;
        } else {
            $default_database['database_data'] = $database_data;
        }

        $FILTER_APP['PROFILE'] = $data_table_new['KARYAWAN']['join_data'][ResponseFormatter::toUUID($users->nrp)] ?? null;
        session()->put('FILTER_APP', $FILTER_APP);
        $default_database['FILTER_APP'] = $FILTER_APP;
        return $default_database;
    }

    public function refreshSession(Request $request)
    {

        /*
            1. validasi apakah default filter ada di session


        */
        $request->validate([
            'auth_token' => 'required|string',
        ]);
        $auth_login = $request->header('token');
        $default_database = $this->refreshSessionProses($auth_login);
        session()->put('DATABASE', $default_database);
        $default_database['SESSION_'] =  session()->all();
        return response()->json([
            'data' => $default_database,
            'status' => 'success',
            'message' => 'Session refreshed successfully code 1',
        ]);
    }

    public function deleteDatadataTable(Request $request)
    {
        $request->validate([
            'group_data' => 'required|string',
        ]);

        $data = $request->all();
        $deleted = [];

        // return ResponseFormatter::ResponseJson($data, 'Data table deleted successfully', 200);
        // Process the data as needed, e.g., delete from database
        // For demonstration, we'll just return the received data
        // delete data table
        if ($data['group_data'] == 'database_tables') {
            // database_tables
            $Q_delete = DatabaseTable::where('code_table', $data['code_data'])->delete();
            $deleted['table'] = $Q_delete;

            // database_fields
            $Q_delete = DatabaseField::where('code_table_field', $data['code_data'])->delete();
            $deleted['field'] = $Q_delete;
            // database_data
            $Q_delete = DatabaseData::where('code_table_data',  $data['code_data'])->delete();
            $deleted['data'] = $Q_delete;
            return ResponseFormatter::ResponseJson($deleted, 'Data table deleted successfully', 200);
        } elseif ($data['group_data'] == 'database_datas') {
            // database_fields
            $Q_delete = DatabaseField::where('code_table_field', $data['code_table'])
                ->where('code_field', $data['code_field'])->delete();

            // database_data
            $Q_delete = DatabaseData::where('code_table_data',  $data['code_table'])
                ->where('code_field_data', $data['code_field'])->delete();
        } else {
            return ResponseFormatter::ResponseJson(null, 'group_data is required', 400);
        }


        return ResponseFormatter::ResponseJson($data, 'Data table deleted successfully', 200);
    }

    public function getTableData($code_table)
    {
        //1.0 GET table properties
        $Q_table = DatabaseTable::where('code_table', $code_table)->get();
        $data_return = [];
        $data_table = [];
        $data_table_child = [];
        foreach ($Q_table as $table) {
            // $data_table['table'] = $table;
            $data_table['all_table'][$table->code_table] = $table;
            $data_table['the_table'] = $table;
            $data_return['table'] = $table;
        }




        // 1.1 GET Fields
        $Q_field = DatabaseField::where('code_table_field', $code_table)->get();
        foreach ($Q_field as $field) {
            // $data_table['fields'][$field->code_field] = $field;
            $data_table['all_fields'][$field->full_code_field] = $field;
            $data_return['fields'][$field->code_table_field] = $field;
        }


        // 2.0 GET data
        $Q_data_table = DatabaseData::where('code_table_data', $code_table)->whereNull('date_end')->get([
            'code_table_data',
            'code_field_data',
            'value_data',
            'code_data',
            'uuid_data'
        ]);

        $data_datatables = [];
        foreach ($Q_data_table  as $data_datatable) {
            $data_datatables[$data_datatable->code_data][$data_datatable->code_field_data] =   $data_datatable;
            $data_table['the_data'][$data_datatable->uuid_data][$data_datatable->code_field_data] =  $data_datatable;
        }
        $data_return['data_tables'] = $data_datatables;


        return ResponseFormatter::ResponseJson($data_return, 'Success Get ' . $code_table, 200);

        $Q_table = DatabaseTable::where('parent_table', $code_table)->get();
        foreach ($Q_table as $table) {
            $Q_field = DatabaseField::where('code_table_field', $table->code_table)->get();
            foreach ($Q_field as $field) {
                // $data_table['child']['table'][$table->code_table]['fields'][$field->code_field] = $field;                
                $data_table['all_fields'][$field->full_code_field] = $field;
            }
            $data_table['child']['table'][$table->code_table]['table'] = $table;
            $data_table['all_table'][$table->code_table] = $table;
        }

        foreach ($data_table['all_fields']  as $arr_field) {
            $data_table['arr_fields'][] =  $arr_field;
            if ($arr_field->code_table_field ==  $data_table['the_table']['code_table']) {
                $data_table['the_fields'][$arr_field->code_field] = $arr_field;
            }
        }

        $data_table['the_table']['fields'] = $data_table['the_fields'];



        // DATA
        $Q_data_table = DatabaseData::where('code_table_data', $code_table)->whereNull('date_end')->get();
        // $data_table['the_data'] = $Q_data_table;
        foreach ($Q_data_table  as $data_datatable) {
            $data_table['the_data'][$data_datatable->uuid_data][$data_datatable->code_field_data] =  $data_datatable;
        }

        $data_table['the_template'] = null;
        dd($data_table);
        return ResponseFormatter::ResponseJson($data_table, 'Success Get ' . $code_table, 200);
    }
}
