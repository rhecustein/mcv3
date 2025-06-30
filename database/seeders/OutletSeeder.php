<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OutletSeeder extends Seeder
{
    public function run()
    { 

        $user = User::firstOrCreate(
            ['email' => 'sry@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA SERAYA',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '82287263557',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA SERAYA',
            'code' => 'SRY',
            'phone' => '82287263557',
            'email' => 'sry@suratsehat.co.id',
            'address' => 'Jl. Budi Kemuliaan Komp. Ruko Abaditama Blok A No. 10',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.1486442649057143,
            'longitude' => 104.10000000,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'btm@suratsehat.co.id'],
            [
                'name' => 'KIMIA FARMA BOTANIA',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81362614522',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KIMIA FARMA BOTANIA',
            'code' => 'BTM',
            'phone' => '81362614522',
            'email' => 'btm@suratsehat.co.id',
            'address' => 'Komplek Ruko Botania Garden Blok A1 No. 1C Kel. Belian',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.1274032036457553,
            'longitude' => 104.10013208906881,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'kda@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA KDA',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81364837873',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA KDA',
            'code' => 'KDA',
            'phone' => '81364837873',
            'email' => 'kda@suratsehat.co.id',
            'address' => 'Jl. Raja Ali Kelana Komp. Pertokoan Griya Kurnia/KDA Blok B No. 34 RT 03 RW 05 Kel. Belian Kec. Batam Kota - Kota Batam',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.1060589422596725,
            'longitude' => 104.08165040562118,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'avr@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA 224 AVIARI',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81372588344',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA 224 AVIARI',
            'code' => 'AVR',
            'phone' => '81372588344',
            'email' => 'avr@suratsehat.co.id',
            'address' => 'Komplek Ruko Muka Kuning Indah II Blok F No. 01 Kel. Buliang Kec. Batu Aji - Kota Batam',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.0492224008157027,
            'longitude' => 103.96939531953652,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'sei@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA 128 SEI HARAPAN',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81268837751',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA 128 SEI HARAPAN',
            'code' => 'SEI',
            'phone' => '81268837751',
            'email' => 'sei@suratsehat.co.id',
            'address' => 'Komplek Wijaya Blok A No. 1-2 Sekupang RT 002 RW 001 Kel. Sungai Harapan',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.1086186773299842,
            'longitude' => 103.95200545056062,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'nag@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA 59 NAGOYA',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '82287389259',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA 59 NAGOYA',
            'code' => 'NAG',
            'phone' => '82287389259',
            'email' => 'nag@suratsehat.co.id',
            'address' => 'Jalan Imam Bonjol Kp. Utama',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.1432417193186324,
            'longitude' => 104.01458812569653,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'sag@suratsehat.co.id'],
            [
                'name' => 'KLINIK UTAMA KIMIA FARMA SAGULUNG BARU',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81218671661',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK UTAMA KIMIA FARMA SAGULUNG BARU',
            'code' => 'SAG',
            'phone' => '81218671661',
            'email' => 'sag@suratsehat.co.id',
            'address' => 'Kavling Sagulung Baru Blok T No.167',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.041015714688631,
            'longitude' => 103.94210701805612,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'gul@suratsehat.co.id'],
            [
                'name' => 'Klinik Kimia Farma 0137 - Sagulung',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81372587504',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'Klinik Kimia Farma 0137 - Sagulung',
            'code' => 'GUL',
            'phone' => '81372587504',
            'email' => 'gul@suratsehat.co.id',
            'address' => 'Komplek Sagulung Mas Indah',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.0482145124035647,
            'longitude' => 103.9517042372254,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'bat@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA 109 - BATU 3',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '85272520452',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA 109 - BATU 3',
            'code' => 'BAT',
            'phone' => '85272520452',
            'email' => 'bat@suratsehat.co.id',
            'address' => 'JL. M.T HARYONO KM 3.5',
            'city' => 'TANJUNGPINANG',
            'is_active' => true,
            'latitude' => 0.9197516560112613,
            'longitude' => 104.4637866158927,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => '   '],
            [
                'name' => 'Klinik Kimia Farma Batam Centre 269',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81277650925',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'Klinik Kimia Farma Batam Centre 269',
            'code' => 'BTC',
            'phone' => '81277650925',
            'email' => 'btc@suratsehat.co.id',
            'address' => 'Jl. Sudirman Jl. Komp. Dian Centre',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.041015714688631,
            'longitude' => 103.94210701805612,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'bin@suratsehat.co.id'],
            [
                'name' => 'Klinik Kimia Farma 297 - Bintan Center',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81333773161',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'Klinik Kimia Farma 297 - Bintan Center',
            'code' => 'BIN',
            'phone' => '81333773161',
            'email' => 'bin@suratsehat.co.id',
            'address' => 'Jl. D.I Panjaitan Km.9 Kota Tanjungpinang',
            'city' => 'Tanjungpinang',
            'is_active' => true,
            'latitude' => 0.916987817802846,
            'longitude' => 104.50973249139395,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'lrz@suratsehat.co.id'],
            [
                'name' => 'KIMIA FARMA LORENZO',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81276595045',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KIMIA FARMA LORENZO',
            'code' => 'LRZ',
            'phone' => '81276595045',
            'email' => 'lrz@suratsehat.co.id',
            'address' => 'komplek ruko lorenzo no 1 pancur piayu',
            'city' => 'BATAM',
            'is_active' => true,
            'latitude' => 1.0249085924971908,
            'longitude' => 104.06404826688257,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'gan@suratsehat.co.id'],
            [
                'name' => 'KIMIA FARMA 599 GANET',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81374287723',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KIMIA FARMA 599 GANET',
            'code' => 'GAN',
            'phone' => '81374287723',
            'email' => 'gan@suratsehat.co.id',
            'address' => 'Jl. Ganet Blok A no. 1-2',
            'city' => 'Tanjungpinang',
            'is_active' => true,
            'latitude' => 0.9228350674562648,
            'longitude' => 104.51152076918046,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'plz@suratsehat.co.id'],
            [
                'name' => 'Klinik Kimia Farma Sp Plaza',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81367293755',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'Klinik Kimia Farma Sp Plaza',
            'code' => 'PLZ',
            'phone' => '81367293755',
            'email' => 'plz@suratsehat.co.id',
            'address' => 'Komplek Sentosa Perdana Blok DD No. 08',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.0423134095218825,
            'longitude' => 103.98139139031619,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'tbs@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA TEMBESI',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '82268700978',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA TEMBESI',
            'code' => 'TBS',
            'phone' => '82268700978',
            'email' => 'tbs@suratsehat.co.id',
            'address' => 'Komplek Pertokoan Buana Impian Blok DD No. 03-04',
            'city' => 'BATAM',
            'is_active' => true,
            'latitude' => 1.0321189106522592,
            'longitude' => 104.00308654606847,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'sgk@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA SIMPANG KARA',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '8,95E+16',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA SIMPANG KARA',
            'code' => 'SGK',
            'phone' => '8,95E+16',
            'email' => 'sgk@suratsehat.co.id',
            'address' => 'Jl. Golden Land Jl. Ahmad Yani No.3',
            'city' => 'BATAM',
            'is_active' => true,
            'latitude' => 1.1104096116478106,
            'longitude' => 104.04099698772617,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'sto@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA 646 SUTOMO',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '82172283132',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA 646 SUTOMO',
            'code' => 'STO',
            'phone' => '82172283132',
            'email' => 'sto@suratsehat.co.id',
            'address' => 'JL. Dr. SUTOMO NO.9 TANJUNGPINANG BARAT',
            'city' => 'TANJUNGPINANG',
            'is_active' => true,
            'latitude' => 0.9174768813021364,
            'longitude' => 104.45110171253522,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'pbl@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA 230 PANBIL',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '82174621900',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA 230 PANBIL',
            'code' => 'PBL',
            'phone' => '82174621900',
            'email' => 'pbl@suratsehat.co.id',
            'address' => 'Ruko Panbil Blok A No.5',
            'city' => 'BATAM',
            'is_active' => true,
            'latitude' => 1.073423221868607,
            'longitude' => 104.02492631346685,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'pdn@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA 169 PAMEDAN',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '82172156002',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA 169 PAMEDAN',
            'code' => 'PDN',
            'phone' => '82172156002',
            'email' => 'pdn@suratsehat.co.id',
            'address' => 'jl. Raja Ali Haji no 7-8 Pamedan Tanjungpinang',
            'city' => 'Tanjung Pinang',
            'is_active' => true,
            'latitude' => 0.9099011092965529,
            'longitude' => 104.46436156284096,
            'timezone' => 'Asia/Jakarta',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'nta@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA NATUNA',
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '912',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA NATUNA',
            'code' => 'NTA',
            'phone' => '912',
            'email' => 'nta@suratsehat.co.id',
            'address' => '',
            'city' => 'BATAM',
            'is_active' => true,
            'latitude' => 3.9405957101285716,
            'longitude' => 108.3865753705898,
            'timezone' => 'Asia/Jakarta',
        ]);
        //Inhose Shimano Muka Kuning	Batamindo Industry Park, Jl. Gaharu No.237 Lot. 235, Muka Kuning, Kec. Sei Beduk, Kota Batam, Kepulauan Riau 29433	1.0621451612156945, 104.03058905527494
        $user = User::firstOrCreate(
            ['email' => 'inh@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA INHORSE SHIMANO MUKA KUNING', 
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81276595045',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA INHORSE SHIMANO MUKA KUNING',
            'code' => 'INH',
            'phone' => '81276595045',
            'email' => 'inh@suratsehat.co.id',
            'address' => 'Batamindo Industry Park, Jl. Gaharu No.237 Lot. 235, Muka Kuning, Kec. Sei Beduk, Kota Batam, Kepulauan Riau 29433',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.0621451612156945,
            'longitude' => 104.03058905527494,
            'timezone' => 'Asia/Jakarta',
        ]);
        //Inhouse Infineon 1	Batamindo Industrial Park Lot. 317, Kabil, Jl. Beringin, Muka Kuning, Kec. Sei Beduk, Kota Batam, Kepulauan Riau 29433	1.058667997051153, 104.03838222590869
        $user = User::firstOrCreate(
            ['email' => 'inf@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA INHOUSE INFINEON 1', 
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81276595045',
            ]
        );

        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA INHOUSE INFINEON 1',
            'code' => 'INF',
            'phone' => '81276595045',
            'email' => 'inf@suratsehat.co.id',
            'address' => 'Batamindo Industrial Park Lot. 317, Kabil, Jl. Beringin, Muka Kuning, Kec. Sei Beduk, Kota Batam, Kepulauan Riau 29433',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.058667997051153,
            'longitude' => 104.03838222590869,
            'timezone' => 'Asia/Jakarta',
        ]);
        //Inhouse Infineon 2	323Q+MV, Kabil, Kecamatan Nongsa, Kota Batam, Kepulauan Riau	1.054313543176812, 104.04076367008771
        $user = User::firstOrCreate(
            ['email' => 'inf@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA INHOUSE INFINEON 2', 
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81276595045',
            ]
        );
        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA INHOUSE INFINEON 2',
            'code' => 'INF2',
            'phone' => '81276595045',
            'email' => 'inf2@suratsehat.co.id',
            'address' => '323Q+MV, Kabil, Kecamatan Nongsa, Kota Batam, Kepulauan Riau',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.054313543176812,
            'longitude' => 104.04076367008771,
            'timezone' => 'Asia/Jakarta',
        ]); 
        //Inhouse Infineon 3	327M+V33, Muka Kuning, Kec. Sei Beduk, Kota Batam, Kepulauan Riau 29433	1.0653144485686183, 104.03276017380396
        $user = User::firstOrCreate(
            ['email' => 'inf3@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA INHOUSE INFINEON 3', 
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81276595045',
            ]
        );
        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA INHOUSE INFINEON 3',
            'code' => 'INF3',
            'phone' => '81276595045',
            'email' => 'inf3@suratsehat.co.id',
            'address' => '327M+V33, Muka Kuning, Kec. Sei Beduk, Kota Batam, Kepulauan Riau 29433',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.0653144485686183,
            'longitude' => 104.03276017380396,
            'timezone' => 'Asia/Jakarta',
        ]);
        //Inhouse Profab	Batu Merah, Kec. Batu Ampar, Kota Batam, Kepulauan Riau	1.1844858318768268, 104.006562362089
        $user = User::firstOrCreate(
            ['email' => 'pro@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA INHOUSE PROFAB', 
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81276595045',
            ]
        );
        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA INHOUSE PROFAB',
            'code' => 'PRO',
            'phone' => '81276595045',
            'email' => 'pro@suratsehat.co.id',
            'address' => 'Batu Merah, Kec. Batu Ampar, Kota Batam, Kepulauan Riau',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.1844858318768268,
            'longitude' => 104.006562362089,
            'timezone' => 'Asia/Jakarta',
        ]);
        //Inhouse Shimano Panbil	Panbil Industrial Estate Factory, Belian, Jl Shimano Jaya No.19 A Lot 10, Muka Kuning, Batam Kota, Batam City, Riau Islands 29433	1.0724609214492258, 104.01929010475362
        $user = User::firstOrCreate(
            ['email' => 'shim@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA INHOUSE SHIMANO PANBIL', 
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81276595045',
            ]
        );
        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA INHOUSE SHIMANO PANBIL',
            'code' => 'SHIM',
            'phone' => '81276595045',
            'email' => 'shim@suratsehat.co.id',
            'address' => 'Panbil Industrial Estate Factory, Belian, Jl Shimano Jaya No.19 A Lot 10, Muka Kuning, Batam Kota, Batam City, Riau Islands 29433',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.0724609214492258,
            'longitude' => 104.01929010475362,
            'timezone' => 'Asia/Jakarta',
        ]);
        //Inhouse TDK	Jalan EPCOS Jaya, Blok B1-10 Kawasan Industri Panbil Muka Kuning, Kabil, Kecamatan Nongsa, Pulau Batam, Kepulauan Riau 29433	1.0761770202820735, 104.02606902268626
        $user = User::firstOrCreate(
            ['email' => 'tdk@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA INHOUSE TDK', 
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81276595045',
            ]
        );
        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA INHOUSE TDK',
            'code' => 'TDK',
            'phone' => '81276595045',
            'email' => 'tdk@suratsehat.co.id',
            'address' => 'Jalan EPCOS Jaya, Blok B1-10 Kawasan Industri Panbil Muka Kuning, Kabil, Kecamatan Nongsa, Pulau Batam, Kepulauan Riau 29433',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.0761770202820735,
            'longitude' => 104.02606902268626,
            'timezone' => 'Asia/Jakarta',
        ]);
        //Laboratorium Nagoya	Jl. Imam Bonjol No.59, Lubuk Baja Kota, Kec. Lubuk Baja, Kota Batam	1.1432417193186324, 104.01458812569653
        $user = User::firstOrCreate(
            ['email' => 'labnagoya@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA LABORATORIUM NAGOYA', 
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81276595045',
            ]
        );
        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA LABORATORIUM NAGOYA',
            'code' => 'NAGY',
            'phone' => '81276595045',
            'email' => 'labnagoya@suratsehat.co.id',
            'address' => 'Jl. Imam Bonjol No.59, Lubuk Baja Kota, Kec. Lubuk Baja, Kota Batam',
            'city' => 'Batam',
            'is_active' => true,
            'latitude' => 1.1432417193186324,
            'longitude' => 104.01458812569653,
            'timezone' => 'Asia/Jakarta',
        ]);
        //Laboratorium Pamedan	Jl. Raja Ali H. No.10, Tj. Pinang Timur, Kec. Bukit Bestari, Kota Tanjung Pinang, Kepulauan Riau 29124	0.9099011092965529, 104.46436156284096
        $user = User::firstOrCreate(
            ['email' => 'labpamedan@suratsehat.co.id'],
            [
                'name' => 'KLINIK KIMIA FARMA LABORATORIUM PAMEDAN', 
                'password' => Hash::make('qwerty123'),
                'role_type' => 'outlet',
                'phone' => '81276595045',
            ]   
        );
        Outlet::create([
            'admin_id' => 1,
            'user_id' => $user->id,
            'name' => 'KLINIK KIMIA FARMA LABORATORIUM PAMEDAN',
            'code' => 'PAMY',
            'phone' => '81276595045',
            'email' => 'labpamedan@suratsehat.co.id',
            'address' => 'Jl. Raja Ali H. No.10, Tj. Pinang Timur, Kec. Bukit Bestari, Kota Tanjung Pinang, Kepulauan Riau 29124',
            'city' => 'Tanjung Pinang',
            'is_active' => true,
            'latitude' => 0.9099011092965529,
            'longitude' => 104.46436156284096,
            'timezone' => 'Asia/Jakarta',
        ]);
    }
}