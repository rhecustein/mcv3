<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'id' => 1000,
            'name' => 'dr. Aussie Griffina Sinulingga',
            'email' => 'doctor1000@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1000,
            'admin_id' => 1,
            'outlet_id' => 1,
            'license_number' => '007.II/001-871/SIP.TM/DPMPTSP-BTM/X/2022',
        ]);

        User::create([
            'id' => 1001,
            'name' => 'dr. Hervina',
            'email' => 'doctor1001@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1001,
            'admin_id' => 1,
            'outlet_id' => 1,
            'license_number' => '014.I/001-840/SIP.TM/DPMPTSP-BTM/X/2023',
        ]);

        User::create([
            'id' => 1002,
            'name' => 'drg. Hana Belinda Katriani',
            'email' => 'doctor1002@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1002,
            'admin_id' => 1,
            'outlet_id' => 1,
            'license_number' => '503.440/004/429.111/2023',
        ]);

        User::create([
            'id' => 1003,
            'name' => 'dr Khanda Abhestiwangga',
            'email' => 'doctor1003@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1003,
            'admin_id' => 1,
            'outlet_id' => 2,
            'license_number' => '021.I/001-491/SIP.TM/DPMPTSP-BTM/VI/2023',
        ]);

        User::create([
            'id' => 1004,
            'name' => 'dr Christine Anggraeni',
            'email' => 'doctor1004@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1004,
            'admin_id' => 1,
            'outlet_id' => 2,
            'license_number' => '014.I/001-261/SIP.TM/DPMPTSP-BTM/VI/2021',
        ]);

        User::create([
            'id' => 1005,
            'name' => 'drg Shelly Ike Indiarti',
            'email' => 'doctor1005@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1005,
            'admin_id' => 1,
            'outlet_id' => 2,
            'license_number' => '001.I/012-047/SIP-TM/DPMPTSP-BTM/II/2021',
        ]);

        User::create([
            'id' => 1006,
            'name' => 'DR. IQRA',
            'email' => 'doctor1006@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1006,
            'admin_id' => 1,
            'outlet_id' => 2,
            'license_number' => '040.III/001-879/SIP.TM/DPMPTSP-BTM/X/2023',
        ]);

        User::create([
            'id' => 1007,
            'name' => 'drg. Wiwi Kardina Saputri',
            'email' => 'doctor1007@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1007,
            'admin_id' => 1,
            'outlet_id' => 2,
            'license_number' => '503.440/007/429.111/2023',
        ]);

        User::create([
            'id' => 1008,
            'name' => 'dr.Lely Ester Juniana Harefa',
            'email' => 'doctor1008@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1008,
            'admin_id' => 1,
            'outlet_id' => 2,
            'license_number' => '503.440/022/429.111/2024',
        ]);

        User::create([
            'id' => 1009,
            'name' => 'dr Samdiharja',
            'email' => 'doctor1009@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1009,
            'admin_id' => 1,
            'outlet_id' => 3,
            'license_number' => '049.1/001-701/SIP.TM/DPMPTSP-BTM/VIII/2022',
        ]);

        User::create([
            'id' => 1010,
            'name' => 'dr Budhi Ardiansyah',
            'email' => 'doctor1010@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1010,
            'admin_id' => 1,
            'outlet_id' => 3,
            'license_number' => '032.II/001-130/SIP.TM/DPMPTSP-BTM/III/2021',
        ]);

        User::create([
            'id' => 1011,
            'name' => 'dr Auusie Griffina Sinuling',
            'email' => 'doctor1011@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1011,
            'admin_id' => 1,
            'outlet_id' => 3,
            'license_number' => '007.II/001-871/SIP.TM/DPMPTSP-BTM/X/2022',
        ]);

        User::create([
            'id' => 1012,
            'name' => 'drg Yudha Agriawan',
            'email' => 'doctor1012@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1012,
            'admin_id' => 1,
            'outlet_id' => 3,
            'license_number' => '006.II/012-456/SIP.TM/DPMPTSP-BTM/IX/2020',
        ]);

        User::create([
            'id' => 1013,
            'name' => 'drg Yelvia Rita',
            'email' => 'doctor1013@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1013,
            'admin_id' => 1,
            'outlet_id' => 3,
            'license_number' => '503.440/009/429.111/2024',
        ]);

        User::create([
            'id' => 1014,
            'name' => 'dr Imelda Sembiring',
            'email' => 'doctor1014@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1014,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => '012.I/001-770/SIP.TM/DPMPTSP-BTM/IX/2022',
        ]);

        User::create([
            'id' => 1015,
            'name' => 'dr Lunnar Deasy Herlina Sipahutar',
            'email' => 'doctor1015@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1015,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => '005.III/001-757/SIP.TM/DPMPTSP-BTM/IX/2022',
        ]);

        User::create([
            'id' => 1016,
            'name' => 'drg Selina Wissen',
            'email' => 'doctor1016@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1016,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => '006.II/012-161/SIP.TM/DPMPTSP-BTM/III/2022',
        ]);

        User::create([
            'id' => 1017,
            'name' => 'drg Yunita Putri Wardani',
            'email' => 'doctor1017@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1017,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => '011.I/012-168/SIP.TM/DPMPTSP-BTM/III/2020',
        ]);

        User::create([
            'id' => 1018,
            'name' => 'dr Roy Martua Munthe',
            'email' => 'doctor1018@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1018,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => '041.II/001-945/SIP.TM/DPMPTSP-BTM/X/2022',
        ]);

        User::create([
            'id' => 1019,
            'name' => 'drg Muhammad Yusuf',
            'email' => 'doctor1019@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1019,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => '004.III/012-342/SIP.TM/DPMPTSP-BTM/VIII/2021',
        ]);

        User::create([
            'id' => 1020,
            'name' => 'dr Rahmat Rivai',
            'email' => 'doctor1020@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1020,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => '031.II/001-510/SIP.TM/DPMPTSP-BTM/VI/2023',
        ]);

        User::create([
            'id' => 1021,
            'name' => 'dr Appendi Hendra Laurensius Sinaga',
            'email' => 'doctor1021@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1021,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => '012.I/001-078/SIP.TM/DPMPTSP-BTM/II/2023',
        ]);

        User::create([
            'id' => 1022,
            'name' => 'dr BANGAR MARUARAR SITANGGANG',
            'email' => 'doctor1022@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1022,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => '031./001-588/SIP.TM/DPMPTSP-BTM/VII/2023',
        ]);

        User::create([
            'id' => 1023,
            'name' => 'dr Merillin Cahyandini',
            'email' => 'doctor1023@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1023,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => '503.440/084/429.111/2023',
        ]);

        User::create([
            'id' => 1024,
            'name' => 'dr Merillin Cahyandini',
            'email' => 'doctor1024@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1024,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => '503.440/084/429.111/2023',
        ]);

        User::create([
            'id' => 1025,
            'name' => 'dr HAZMAN RIZALDI',
            'email' => 'doctor1025@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1025,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => '503.440/039/429.111/2024',
        ]);

        User::create([
            'id' => 1026,
            'name' => 'dr. Elisnawati Br Purba',
            'email' => 'doctor1026@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1026,
            'admin_id' => 1,
            'outlet_id' => 5,
            'license_number' => '001.I/001-617/SIP.TM/DPMPTSP-BTM/VIII/2022',
        ]);

        User::create([
            'id' => 1027,
            'name' => 'drg. Eni Sulistyaningsih',
            'email' => 'doctor1027@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1027,
            'admin_id' => 1,
            'outlet_id' => 5,
            'license_number' => '006.II/012-671/SIP.TM/DPMPTSP-BTM/VIII/2022',
        ]);

        User::create([
            'id' => 1028,
            'name' => 'drg. Yunita Putri Wardani',
            'email' => 'doctor1028@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1028,
            'admin_id' => 1,
            'outlet_id' => 5,
            'license_number' => '002.II/012-181/SIP.TM/DPMPTSP-BTM/IV/2020',
        ]);

        User::create([
            'id' => 1029,
            'name' => 'dr. Appendi Hendra Laurensius Sinaga',
            'email' => 'doctor1029@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1029,
            'admin_id' => 1,
            'outlet_id' => 5,
            'license_number' => '503.440/085/429.111/2023',
        ]);

        User::create([
            'id' => 1030,
            'name' => 'dr.iqra',
            'email' => 'doctor1030@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1030,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '029.I/001-508/SIP.TM?DPMPTSP-BTM/VI/2023',
        ]);

        User::create([
            'id' => 1031,
            'name' => 'dr. Rohadi Pratama Siregar',
            'email' => 'doctor1031@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1031,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '017.II/001-441/SIP.TM/DPMPTSP-BTM/X/2021',
        ]);

        User::create([
            'id' => 1032,
            'name' => 'dr. Stevephanus Raygard',
            'email' => 'doctor1032@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1032,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '024.I/001-407/SIP.TM/DPMPTSP-BTM/V/2022',
        ]);

        User::create([
            'id' => 1033,
            'name' => 'dr.Imam Maulana',
            'email' => 'doctor1033@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1033,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '047.I/001-955/SIP.TM/DPMPTSP-BTM/X/2022',
        ]);

        User::create([
            'id' => 1034,
            'name' => 'dr.Khanda Abhestiwangga',
            'email' => 'doctor1034@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1034,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '023.III/001-495/SIP.TM/DPMPTSP-BTM/VI/2023',
        ]);

        User::create([
            'id' => 1035,
            'name' => 'drg. Wilda Agustina',
            'email' => 'doctor1035@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1035,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '026.1/012-026/SIP.TM/DPMPTSP-BTM/V/2018',
        ]);

        User::create([
            'id' => 1036,
            'name' => 'drg. Rismaulina Sitanggang',
            'email' => 'doctor1036@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1036,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '011.II/012/SIP.TM/DPMTSP-BTM/III/2022',
        ]);

        User::create([
            'id' => 1037,
            'name' => 'dr. Dian Marta Sari',
            'email' => 'doctor1037@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1037,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '049.I/001-543/SIP.TM/DPMTSP-BTM/VI/2023',
        ]);

        User::create([
            'id' => 1038,
            'name' => 'dr. Merillin Cahyandini',
            'email' => 'doctor1038@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1038,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '503.440/025/429.111/2024',
        ]);

        User::create([
            'id' => 1039,
            'name' => 'dr Syahroni',
            'email' => 'doctor1039@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1039,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '503.440/084/429.111/2024',
        ]);

        User::create([
            'id' => 1040,
            'name' => 'dr Anggelus Hondro Bidaya',
            'email' => 'doctor1040@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1040,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '001.1/001-001/SIP.TM/DPMPTSP-BTm/I/2023',
        ]);

        User::create([
            'id' => 1041,
            'name' => 'drg .Eni Sulistyaningsih',
            'email' => 'doctor1041@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1041,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '94/SIPDGSA/DPMPTSP-BTM/01/XI/2024',
        ]);

        User::create([
            'id' => 1042,
            'name' => 'DR KEVIN CHANIAGO TANUDIRJO',
            'email' => 'doctor1042@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1042,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => 'MR21712412412003753',
        ]);

        User::create([
            'id' => 1043,
            'name' => 'dr. Dessy Ariyeni, Sp.M',
            'email' => 'doctor1043@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1043,
            'admin_id' => 1,
            'outlet_id' => 7,
            'license_number' => '001.II/004-150/SIP.TM/DPMPTSP-BTM/III/2020',
        ]);

        User::create([
            'id' => 1044,
            'name' => 'dr. Herlina Prajatmo, Sp.OG',
            'email' => 'doctor1044@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1044,
            'admin_id' => 1,
            'outlet_id' => 7,
            'license_number' => '001.III/002-320/SIP.TM/DPMPTSP-BTM/VII/2020',
        ]);

        User::create([
            'id' => 1045,
            'name' => 'dr. Kurniakin Walrisman Sahata Girsang, Sp,PD',
            'email' => 'doctor1045@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1045,
            'admin_id' => 1,
            'outlet_id' => 7,
            'license_number' => '002.III/007-1100/SIP.TM/DPMPTSP-BTM/XII/2022',
        ]);

        User::create([
            'id' => 1046,
            'name' => 'dr. Rianda Putra',
            'email' => 'doctor1046@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1046,
            'admin_id' => 1,
            'outlet_id' => 7,
            'license_number' => '015.I/001-097/SIP.TM/DPMPTSP-BTM/II/2022',
        ]);

        User::create([
            'id' => 1047,
            'name' => 'dr. Silvia Mandayani',
            'email' => 'doctor1047@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1047,
            'admin_id' => 1,
            'outlet_id' => 7,
            'license_number' => '053.I/001-961/SIP.TM/DPMPTSP-BTM/X/2022',
        ]);

        User::create([
            'id' => 1048,
            'name' => 'dr.Christian Agus Bonasatria Sidabutar',
            'email' => 'doctor1048@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1048,
            'admin_id' => 1,
            'outlet_id' => 7,
            'license_number' => '034.III/001-215/SIP.TM/DPMPTSP-BTM/III/2022',
        ]);

        User::create([
            'id' => 1049,
            'name' => 'dr.Khairina Soraya',
            'email' => 'doctor1049@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1049,
            'admin_id' => 1,
            'outlet_id' => 7,
            'license_number' => '016.II/001-330/SIP.TM/DPMPTSP-BTM/VIII/2021',
        ]);

        User::create([
            'id' => 1050,
            'name' => 'dr. Aulia Ihsani, Sp.PD',
            'email' => 'doctor1050@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1050,
            'admin_id' => 1,
            'outlet_id' => 7,
            'license_number' => '54/SIPDSS/DPMPTSP-BTM/01/V/2024',
        ]);

        User::create([
            'id' => 1051,
            'name' => 'dr Arief Fadhillah',
            'email' => 'doctor1051@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1051,
            'admin_id' => 1,
            'outlet_id' => 8,
            'license_number' => '031.II/001-516/SIP.TM/DPMPTSP-BTM/VI/2022',
        ]);

        User::create([
            'id' => 1052,
            'name' => 'dr Isabella Suhena',
            'email' => 'doctor1052@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1052,
            'admin_id' => 1,
            'outlet_id' => 8,
            'license_number' => '081.II/001-481-SIP.TM/DPMPTSP-BTM/VI/2023',
        ]);

        User::create([
            'id' => 1053,
            'name' => 'dr Silvia Mandayani',
            'email' => 'doctor1053@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1053,
            'admin_id' => 1,
            'outlet_id' => 8,
            'license_number' => '022.II/001-494/SIP.TM/DPMPTSP-BTM/VI/2023',
        ]);

        User::create([
            'id' => 1054,
            'name' => 'drg. Tri Ratna Herawati',
            'email' => 'doctor1054@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1054,
            'admin_id' => 1,
            'outlet_id' => 8,
            'license_number' => '003.I/012-009/SIP-TM/DPMPTSP-BTM/I/2022',
        ]);

        User::create([
            'id' => 1055,
            'name' => 'drg. Annisa Mayang Rusdi',
            'email' => 'doctor1055@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1055,
            'admin_id' => 1,
            'outlet_id' => 8,
            'license_number' => '008.II/012-507/SIP.TM/DPMPTSP-BTM/VI/2023',
        ]);

        User::create([
            'id' => 1056,
            'name' => 'dr Samson Leon S',
            'email' => 'doctor1056@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1056,
            'admin_id' => 1,
            'outlet_id' => 8,
            'license_number' => '003.II/001-548/SIP.TM/DPMPTSP-BTM/VII/2023',
        ]);

        User::create([
            'id' => 1057,
            'name' => 'dr. Tiopan Tarigan',
            'email' => 'doctor1057@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1057,
            'admin_id' => 1,
            'outlet_id' => 8,
            'license_number' => '503.440/075/429.1112023',
        ]);

        User::create([
            'id' => 1058,
            'name' => 'dr. Cici Ariesta Sari',
            'email' => 'doctor1058@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1058,
            'admin_id' => 1,
            'outlet_id' => 8,
            'license_number' => '503.440/013/429.111/2024',
        ]);

        User::create([
            'id' => 1059,
            'name' => 'dr Dewi Sisca Fictory',
            'email' => 'doctor1059@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1059,
            'admin_id' => 1,
            'outlet_id' => 8,
            'license_number' => '123/SIPDUS/DPMPTSP-BTM/01/VII/2024',
        ]);

        User::create([
            'id' => 1060,
            'name' => 'dr. Muhamad Agung Satria',
            'email' => 'doctor1060@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1060,
            'admin_id' => 1,
            'outlet_id' => 8,
            'license_number' => '61/SIPDUS/DPMPTSP-BTM/02/IX/2024',
        ]);

        User::create([
            'id' => 1061,
            'name' => 'dr Rachindi Qory Trysia',
            'email' => 'doctor1061@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1061,
            'admin_id' => 1,
            'outlet_id' => 9,
            'license_number' => '021.I/001-176/SIP.TM/DPMPTSP-BTM/III/2022',
        ]);

        User::create([
            'id' => 1062,
            'name' => 'drg. Rica Alfitri',
            'email' => 'doctor1062@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1062,
            'admin_id' => 1,
            'outlet_id' => 9,
            'license_number' => '014.II/012-944/SIP.TM/DPMPTSP-BTM/X/2022',
        ]);

        User::create([
            'id' => 1063,
            'name' => 'dr. Anggelus Hondro Bidaya',
            'email' => 'doctor1063@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1063,
            'admin_id' => 1,
            'outlet_id' => 9,
            'license_number' => '014.II/001-564/SIP.TM/DPMPTSP-BTM/VII/2023',
        ]);

        User::create([
            'id' => 1064,
            'name' => 'dr. Muhamad Paisal Bin Samsul Bahri',
            'email' => 'doctor1064@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1064,
            'admin_id' => 1,
            'outlet_id' => 10,
            'license_number' => '503/36/5.10.04.01/2023',
        ]);

        User::create([
            'id' => 1065,
            'name' => 'drg. Galih Probowati',
            'email' => 'doctor1065@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1065,
            'admin_id' => 1,
            'outlet_id' => 10,
            'license_number' => '503/329/5.10.04.01/2023',
        ]);

        User::create([
            'id' => 1066,
            'name' => 'dr. Bulan Handestiany',
            'email' => 'doctor1066@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1066,
            'admin_id' => 1,
            'outlet_id' => 10,
            'license_number' => '503/02/5.10.04.01/2024',
        ]);

        User::create([
            'id' => 1067,
            'name' => 'drg. Mardiansyah, M.Kes',
            'email' => 'doctor1067@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1067,
            'admin_id' => 1,
            'outlet_id' => 10,
            'license_number' => '503/241/5.10.04.01/2022',
        ]);

        User::create([
            'id' => 1068,
            'name' => 'dr. Cindy Rachel Christine.S',
            'email' => 'doctor1068@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1068,
            'admin_id' => 1,
            'outlet_id' => 10,
            'license_number' => 'MR21722412000602',
        ]);

        User::create([
            'id' => 1069,
            'name' => 'dr Jolly',
            'email' => 'doctor1069@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1069,
            'admin_id' => 1,
            'outlet_id' => 11,
            'license_number' => '020.II/001-110/SIP.TM/DPMPTSP-BTM/III/2021',
        ]);

        User::create([
            'id' => 1070,
            'name' => 'dr Imam Maulana',
            'email' => 'doctor1070@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1070,
            'admin_id' => 1,
            'outlet_id' => 11,
            'license_number' => '001.II/001-101/SIP.TM/DPMTPSP-BTM/III/2020',
        ]);

        User::create([
            'id' => 1071,
            'name' => 'dr Elisnawati Br Purba',
            'email' => 'doctor1071@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1071,
            'admin_id' => 1,
            'outlet_id' => 11,
            'license_number' => '001.1/001-617/SIP.TM/DPMPTSP-BTM/VIII/2022',
        ]);

        User::create([
            'id' => 1072,
            'name' => 'drg. Muhamad Ridho Saputra',
            'email' => 'doctor1072@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1072,
            'admin_id' => 1,
            'outlet_id' => 11,
            'license_number' => '005.II/012-322/SIP.TM/DPMPTSP-BTM/VII/2020',
        ]);

        User::create([
            'id' => 1073,
            'name' => 'dr.Anggelus Hondro Bidaya',
            'email' => 'doctor1073@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1073,
            'admin_id' => 1,
            'outlet_id' => 11,
            'license_number' => '001.I001-001/SIP.TM/DPMPTSP-BTM/I/2023',
        ]);

        User::create([
            'id' => 1074,
            'name' => 'dr Hazman Rizaldi',
            'email' => 'doctor1074@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1074,
            'admin_id' => 1,
            'outlet_id' => 11,
            'license_number' => '503.440/085/429.111/2024',
        ]);

        User::create([
            'id' => 1075,
            'name' => 'drg IHUT HAMONANGAN',
            'email' => 'doctor1075@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1075,
            'admin_id' => 1,
            'outlet_id' => 11,
            'license_number' => 'NO.48/SIPDGSA/DPMPTSP-BTM/01/VII/2024',
        ]);

        User::create([
            'id' => 1076,
            'name' => 'dr. Rahma Saputra Singga',
            'email' => 'doctor1076@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1076,
            'admin_id' => 1,
            'outlet_id' => 12,
            'license_number' => '503/86/5.10.04.54/2019',
        ]);

        User::create([
            'id' => 1077,
            'name' => 'dr. Cindy Rachel Christine. S',
            'email' => 'doctor1077@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1077,
            'admin_id' => 1,
            'outlet_id' => 12,
            'license_number' => '503/82/5.10.04.01/2023',
        ]);

        User::create([
            'id' => 1078,
            'name' => 'dr. Cakra Diningrat',
            'email' => 'doctor1078@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1078,
            'admin_id' => 1,
            'outlet_id' => 12,
            'license_number' => '503/208/5.10.04.01/2023',
        ]);

        User::create([
            'id' => 1079,
            'name' => 'drg. Naftalina',
            'email' => 'doctor1079@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1079,
            'admin_id' => 1,
            'outlet_id' => 12,
            'license_number' => '-',
        ]);

        User::create([
            'id' => 1080,
            'name' => 'dr. Meice Fitrina Sp.OG',
            'email' => 'doctor1080@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1080,
            'admin_id' => 1,
            'outlet_id' => 12,
            'license_number' => '503.440/009/429.111/2024',
        ]);

        User::create([
            'id' => 1081,
            'name' => 'dr. Reka Metha refiana',
            'email' => 'doctor1081@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1081,
            'admin_id' => 1,
            'outlet_id' => 12,
            'license_number' => '503.440/042/429.111/2024',
        ]);

        User::create([
            'id' => 1082,
            'name' => 'drg.CHRISTEL PELUPESSY',
            'email' => 'doctor1082@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1082,
            'admin_id' => 1,
            'outlet_id' => 12,
            'license_number' => '503/219/5.10.04.38/2021',
        ]);

        User::create([
            'id' => 1083,
            'name' => 'dr. Erfika Yuliza',
            'email' => 'doctor1083@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1083,
            'admin_id' => 1,
            'outlet_id' => 12,
            'license_number' => 'MR21722412008794',
        ]);

        User::create([
            'id' => 1084,
            'name' => 'dr. Auliangi Tamayo, Sp.PD',
            'email' => 'doctor1084@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1084,
            'admin_id' => 1,
            'outlet_id' => 12,
            'license_number' => '503/53/5.10.04.01/2024',
        ]);

        User::create([
            'id' => 1085,
            'name' => 'dr. Roy Manurung',
            'email' => 'doctor1085@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1085,
            'admin_id' => 1,
            'outlet_id' => 13,
            'license_number' => '055.I/001-628/SIP.TM/DPMPTSP-BTM/VII/2023',
        ]);

        User::create([
            'id' => 1086,
            'name' => 'dr. Setiawati',
            'email' => 'doctor1086@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1086,
            'admin_id' => 1,
            'outlet_id' => 13,
            'license_number' => '000.III/001-450/SIP.TM/DPMPTSP-BTM/XI/2020',
        ]);

        User::create([
            'id' => 1087,
            'name' => 'dr. Roy Manurung',
            'email' => 'doctor1087@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1087,
            'admin_id' => 1,
            'outlet_id' => 13,
            'license_number' => '055.I/001-628/SIP.TM/DPMPTSP-BTM/VII/2023',
        ]);

        User::create([
            'id' => 1088,
            'name' => 'DRG. YULANDA AGRYANI',
            'email' => 'doctor1088@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1088,
            'admin_id' => 1,
            'outlet_id' => 13,
            'license_number' => '005.I/012-447/SIP.TM/DPMPTSP-BTM/IX/2020',
        ]);

        User::create([
            'id' => 1089,
            'name' => 'DRG. NOVA LISARTI',
            'email' => 'doctor1089@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1089,
            'admin_id' => 1,
            'outlet_id' => 13,
            'license_number' => '011.I/012-505/SIP.TM/DPMPTSP-BTM/X/2020',
        ]);

        User::create([
            'id' => 1090,
            'name' => 'dr. DIAN MARTA SARI',
            'email' => 'doctor1090@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1090,
            'admin_id' => 1,
            'outlet_id' => 13,
            'license_number' => '012.II/001-561/SIP.TM/DPMPTSP-BTM/VII/2023',
        ]);

        User::create([
            'id' => 1091,
            'name' => 'dr. Roy Manurung',
            'email' => 'doctor1091@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1091,
            'admin_id' => 1,
            'outlet_id' => 13,
            'license_number' => '055.I/001-628/SIP.TM/DPMPTSP-BTM/VII/2023',
        ]);

        User::create([
            'id' => 1092,
            'name' => 'dr. Hermansyah',
            'email' => 'doctor1092@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1092,
            'admin_id' => 1,
            'outlet_id' => 13,
            'license_number' => '017.I/001-844/SIP.TM/DPMPTSP-BTM/X/2023',
        ]);

        User::create([
            'id' => 1093,
            'name' => 'dr. Tera Anjani, M.Kes',
            'email' => 'doctor1093@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1093,
            'admin_id' => 1,
            'outlet_id' => 14,
            'license_number' => '503/163/5.10.04.54/2019',
        ]);

        User::create([
            'id' => 1094,
            'name' => 'drg. Ade Apriedi Syaputra',
            'email' => 'doctor1094@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1094,
            'admin_id' => 1,
            'outlet_id' => 14,
            'license_number' => '503/126/5.10.04.01/2023',
        ]);

        User::create([
            'id' => 1095,
            'name' => 'dr. Cakra Diningrat',
            'email' => 'doctor1095@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1095,
            'admin_id' => 1,
            'outlet_id' => 14,
            'license_number' => '503/218/5.10.04.01/2023',
        ]);

        User::create([
            'id' => 1096,
            'name' => 'dr. Erfika Yuliza',
            'email' => 'doctor1096@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1096,
            'admin_id' => 1,
            'outlet_id' => 14,
            'license_number' => '503/252/5.10.04.01/2023',
        ]);

        User::create([
            'id' => 1097,
            'name' => 'dr. Nurfika Malinda',
            'email' => 'doctor1097@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1097,
            'admin_id' => 1,
            'outlet_id' => 14,
            'license_number' => '-',
        ]);

        User::create([
            'id' => 1098,
            'name' => 'dr. Roy Martua Munthe',
            'email' => 'doctor1098@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1098,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => '042.I/001-946/SIP.TM/DPMPTSP-BTM/X/2022',
        ]);

        User::create([
            'id' => 1099,
            'name' => 'dr. Sondang Dewi Pandiangan',
            'email' => 'doctor1099@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1099,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => '1.163.I/001-363/SIP.TM/DKK/X/2017',
        ]);

        User::create([
            'id' => 1100,
            'name' => 'drg wirdalina',
            'email' => 'doctor1100@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1100,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => '001.II/012-348/SIP.TM/DPMPTSP-BTM/IX/2021',
        ]);

        User::create([
            'id' => 1101,
            'name' => 'dr. Kiki Marina',
            'email' => 'doctor1101@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1101,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => '033.II/001-513/SIP.TM/DPMPTSP-BTM/VI/2023',
        ]);

        User::create([
            'id' => 1102,
            'name' => 'dr.Julia Chrissanty Tiarida Panjaitan',
            'email' => 'doctor1102@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1102,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => 'MR21712409002505',
        ]);

        User::create([
            'id' => 1103,
            'name' => 'drg. SELLY KASUARINA',
            'email' => 'doctor1103@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1103,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => 'MR21712409001740',
        ]);

        User::create([
            'id' => 1104,
            'name' => 'dr.Nelly Endang Karlina Manurung',
            'email' => 'doctor1104@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1104,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => '038./001-597/SIP.TM/DPMPTSP-BTM/VII/2023',
        ]);

        User::create([
            'id' => 1105,
            'name' => 'dr Cici Ariesta Sari,MKM',
            'email' => 'doctor1105@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1105,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => '503.440/014/429.111/2024',
        ]);

        User::create([
            'id' => 1106,
            'name' => 'drg. ANNISA DWI CANTIKA',
            'email' => 'doctor1106@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1106,
            'admin_id' => 1,
            'outlet_id' => 16,
            'license_number' => '005.II/012-669/SIP.TM/DPMPTSP-BTM/VIII/2022',
        ]);

        User::create([
            'id' => 1107,
            'name' => 'dr. RAHMAT RIVAI',
            'email' => 'doctor1107@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1107,
            'admin_id' => 1,
            'outlet_id' => 16,
            'license_number' => '023.I/001-029/SIP.TM/DPMPTSP-BTM/I/2023',
        ]);

        User::create([
            'id' => 1108,
            'name' => 'drg. ELLISA NOVI ELDON',
            'email' => 'doctor1108@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1108,
            'admin_id' => 1,
            'outlet_id' => 16,
            'license_number' => '011.I/012-011/SIP.TM/DPMTSP-BTM/II-2019',
        ]);

        User::create([
            'id' => 1109,
            'name' => 'dr.Bangar Maruarar Sitanggang',
            'email' => 'doctor1109@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1109,
            'admin_id' => 1,
            'outlet_id' => 16,
            'license_number' => '031.I/001-588/SIP.TM/DPMPTSP-BTM/VII/2023',
        ]);

        User::create([
            'id' => 1110,
            'name' => 'drg.Elsyi suryana',
            'email' => 'doctor1110@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1110,
            'admin_id' => 1,
            'outlet_id' => 16,
            'license_number' => '012.1/012.533/sip.TM.DPMTSP-BTM/VI/2023',
        ]);

        User::create([
            'id' => 1111,
            'name' => 'dr. Tiopan Tarigan',
            'email' => 'doctor1111@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1111,
            'admin_id' => 1,
            'outlet_id' => 16,
            'license_number' => '503.440/083/429.111/2023',
        ]);

        User::create([
            'id' => 1112,
            'name' => 'DRG SALLY TRIPAYOMI',
            'email' => 'doctor1112@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1112,
            'admin_id' => 1,
            'outlet_id' => 16,
            'license_number' => '47/SIPDGSA/DPMPTSP-BTM/01/VII/2024',
        ]);

        User::create([
            'id' => 1113,
            'name' => 'dr. Iqra',
            'email' => 'doctor1113@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1113,
            'admin_id' => 1,
            'outlet_id' => 17,
            'license_number' => '005.II/001-144/SIPP.TM/DPMPTSP-BTM/III/2022',
        ]);

        User::create([
            'id' => 1114,
            'name' => 'dr. Nola Agina Ginting',
            'email' => 'doctor1114@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1114,
            'admin_id' => 1,
            'outlet_id' => 17,
            'license_number' => '503.440/009/429.111/2023',
        ]);

        User::create([
            'id' => 1115,
            'name' => 'drg. YELVIA RITA',
            'email' => 'doctor1115@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1115,
            'admin_id' => 1,
            'outlet_id' => 17,
            'license_number' => '503.440/014/429.111/2023',
        ]);

        User::create([
            'id' => 1116,
            'name' => 'dr. Nur Yani Agustina Br Manulang',
            'email' => 'doctor1116@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1116,
            'admin_id' => 1,
            'outlet_id' => 17,
            'license_number' => '503.440/047/429.111/2024',
        ]);

        User::create([
            'id' => 1117,
            'name' => 'dr.Eko saputra',
            'email' => 'doctor1117@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1117,
            'admin_id' => 1,
            'outlet_id' => 17,
            'license_number' => '503.440/076/429.111/2024',
        ]);

        User::create([
            'id' => 1118,
            'name' => 'drg. SOUFA DANELLA',
            'email' => 'doctor1118@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1118,
            'admin_id' => 1,
            'outlet_id' => 18,
            'license_number' => '503/169/5.10.04.01/2023',
        ]);

        User::create([
            'id' => 1119,
            'name' => 'dr. Yudi Yulianto',
            'email' => 'doctor1119@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1119,
            'admin_id' => 1,
            'outlet_id' => 18,
            'license_number' => '503/132/5.10.04.54/2019',
        ]);

        User::create([
            'id' => 1120,
            'name' => 'dr. ROSITA YANTI, Sp.P.D',
            'email' => 'doctor1120@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1120,
            'admin_id' => 1,
            'outlet_id' => 18,
            'license_number' => '503/175/5.10.04.38/2021',
        ]);

        User::create([
            'id' => 1121,
            'name' => 'dr. Bulan Handestiany',
            'email' => 'doctor1121@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1121,
            'admin_id' => 1,
            'outlet_id' => 18,
            'license_number' => '503/145/5.10.04.54.2019',
        ]);

        User::create([
            'id' => 1122,
            'name' => 'dr. SARI REZEKI',
            'email' => 'doctor1122@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1122,
            'admin_id' => 1,
            'outlet_id' => 18,
            'license_number' => '503/276/5.10.04.01/2023',
        ]);

        User::create([
            'id' => 1123,
            'name' => 'drg. Zealin Thamia',
            'email' => 'doctor1123@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1123,
            'admin_id' => 1,
            'outlet_id' => 18,
            'license_number' => '503.440/009/429.111/2024',
        ]);

        User::create([
            'id' => 1124,
            'name' => 'Dr cindy rachel christine s',
            'email' => 'doctor1124@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1124,
            'admin_id' => 1,
            'outlet_id' => 20,
            'license_number' => '503/130/5.10.04.01/2022',
        ]);

        User::create([
            'id' => 1125,
            'name' => 'dr. JUFRIALDY, Sp.B',
            'email' => 'doctor1125@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1125,
            'admin_id' => 1,
            'outlet_id' => 20,
            'license_number' => '503 / 289 / 5.10.04.01 / 2022',
        ]);

        User::create([
            'id' => 1126,
            'name' => 'dr. ADRIAN TAUFIK',
            'email' => 'doctor1126@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1126,
            'admin_id' => 1,
            'outlet_id' => 20,
            'license_number' => '503 / 203 / 5.10.04.01 / 2022',
        ]);

        User::create([
            'id' => 1127,
            'name' => 'dr. ERFIKA YULIZA',
            'email' => 'doctor1127@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1127,
            'admin_id' => 1,
            'outlet_id' => 20,
            'license_number' => '503 / 251 / 5.10.04.01 / 2023',
        ]);

        User::create([
            'id' => 1128,
            'name' => 'dr. Yunita.Sp.KJ',
            'email' => 'doctor1128@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1128,
            'admin_id' => 1,
            'outlet_id' => 20,
            'license_number' => '503/19/5.10.04.38/2020',
        ]);

        User::create([
            'id' => 1129,
            'name' => 'dr Rohadi Pratama Siregar',
            'email' => 'doctor1129@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1129,
            'admin_id' => 1,
            'outlet_id' => 21,
            'license_number' => '503.440/035/429.111/2024',
        ]);

        User::create([
            'id' => 1130,
            'name' => 'dr Hazman Rizaldi',
            'email' => 'doctor1130@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1130,
            'admin_id' => 1,
            'outlet_id' => 21,
            'license_number' => '503.440/039/429.111/2024',
        ]);

        User::create([
            'id' => 1131,
            'name' => 'dr. Septiana Budhi Indarti',
            'email' => 'doctor1131@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1131,
            'admin_id' => 1,
            'outlet_id' => 22,
            'license_number' => '0035/DPMPTSP.21.03/SIP-DOKTER/III/2024',
        ]);

        User::create([
            'id' => 1132,
            'name' => 'dr. Ellys Tahnia Siagian',
            'email' => 'doctor1132@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1132,
            'admin_id' => 1,
            'outlet_id' => 14,
            'license_number' => 'MR2172251008114',
        ]);

        User::create([
            'id' => 1133,
            'name' => 'DR. HAZMAN RIZALDI',
            'email' => 'doctor1133@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1133,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => '503.440/039/429.111/2024',
        ]);

        User::create([
            'id' => 1134,
            'name' => 'dr. TERA ANJANI',
            'email' => 'doctor1134@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1134,
            'admin_id' => 1,
            'outlet_id' => 10,
            'license_number' => 'MR21722502007425',
        ]);

        User::create([
            'id' => 1135,
            'name' => 'dr . Kevin Chaniago Tanudirjo',
            'email' => 'doctor1135@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1135,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => '503.440/055/429.111/2024',
        ]);

        User::create([
            'id' => 1136,
            'name' => 'dr Muhamad Agung Satria',
            'email' => 'doctor1136@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1136,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => 'MR21712501000256',
        ]);

        User::create([
            'id' => 1137,
            'name' => 'dr. Cindy Rachel Christine.S',
            'email' => 'doctor1137@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1137,
            'admin_id' => 1,
            'outlet_id' => 14,
            'license_number' => 'MR21722502009965',
        ]);

        User::create([
            'id' => 1138,
            'name' => 'DR RIRI PERMATA SARI',
            'email' => 'doctor1138@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1138,
            'admin_id' => 1,
            'outlet_id' => 6,
            'license_number' => '503.440/008/429.111/2024',
        ]);

        User::create([
            'id' => 1139,
            'name' => 'dr Cahyani Shintia',
            'email' => 'doctor1139@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1139,
            'admin_id' => 1,
            'outlet_id' => 5,
            'license_number' => 'MR21712502002203',
        ]);

        User::create([
            'id' => 1140,
            'name' => 'dr. Cahyani Shintia',
            'email' => 'doctor1140@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1140,
            'admin_id' => 1,
            'outlet_id' => 1,
            'license_number' => 'MR21712503005487',
        ]);

        User::create([
            'id' => 1141,
            'name' => 'Drg. PEGGY HABRIKA',
            'email' => 'doctor1141@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1141,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => '503/79/SIPD/III.15/2022',
        ]);

        User::create([
            'id' => 1142,
            'name' => 'dr. Sari Rezeki',
            'email' => 'doctor1142@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1142,
            'admin_id' => 1,
            'outlet_id' => 12,
            'license_number' => '-',
        ]);

        User::create([
            'id' => 1143,
            'name' => 'drg. MERISSA',
            'email' => 'doctor1143@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1143,
            'admin_id' => 1,
            'outlet_id' => 2,
            'license_number' => 'MR21712503009950',
        ]);

        User::create([
            'id' => 1144,
            'name' => 'drg. MERRISA',
            'email' => 'doctor1144@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1144,
            'admin_id' => 1,
            'outlet_id' => 13,
            'license_number' => 'MR21712504000616',
        ]);

        User::create([
            'id' => 1145,
            'name' => 'dr  NITA KARMILA',
            'email' => 'doctor1145@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1145,
            'admin_id' => 1,
            'outlet_id' => 19,
            'license_number' => '503.440/084/429.111/2023',
        ]);

        User::create([
            'id' => 1146,
            'name' => 'DR HAZMAN RIZALDI',
            'email' => 'doctor1146@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1146,
            'admin_id' => 1,
            'outlet_id' => 19,
            'license_number' => '503.440/039/429.111/2024',
        ]);

        User::create([
            'id' => 1147,
            'name' => 'Drg. FATIMAH RINI DWININGRUM',
            'email' => 'doctor1147@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1147,
            'admin_id' => 1,
            'outlet_id' => 19,
            'license_number' => '14/SIPDGSA/DPMPTSP-BTM/01/II/2025',
        ]);

        User::create([
            'id' => 1148,
            'name' => 'FIRA MEDLIA SARI',
            'email' => 'doctor1148@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1148,
            'admin_id' => 1,
            'outlet_id' => 19,
            'license_number' => '86/SIPDUS/DPMPTSP-BTM/01/V/2024',
        ]);

        User::create([
            'id' => 1149,
            'name' => 'ANGGELUS HONDRO BIDAYA',
            'email' => 'doctor1149@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1149,
            'admin_id' => 1,
            'outlet_id' => 19,
            'license_number' => '001.1/001-001/SIP.TM/DPMPTSP-BTM/1/2023',
        ]);

        User::create([
            'id' => 1150,
            'name' => 'drg Edo Prasetiawan',
            'email' => 'doctor1150@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1150,
            'admin_id' => 1,
            'outlet_id' => 4,
            'license_number' => 'MR21712504007078',
        ]);

        User::create([
            'id' => 1151,
            'name' => 'dr. Desi Kristina Br Sinaga',
            'email' => 'doctor1151@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1151,
            'admin_id' => 1,
            'outlet_id' => 5,
            'license_number' => 'mr2171250401150',
        ]);

        User::create([
            'id' => 1152,
            'name' => 'dr. LU LYDIA SYLVIA PUTRI',
            'email' => 'doctor1152@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1152,
            'admin_id' => 1,
            'outlet_id' => 12,
            'license_number' => 'MR2172250202538',
        ]);

        User::create([
            'id' => 1153,
            'name' => 'dr. Kevin Chaniago Tanudirjo',
            'email' => 'doctor1153@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1153,
            'admin_id' => 1,
            'outlet_id' => 1,
            'license_number' => 'MR21712505008996',
        ]);

        User::create([
            'id' => 1154,
            'name' => 'drg. Muliza Ganda Putra',
            'email' => 'doctor1154@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1154,
            'admin_id' => 1,
            'outlet_id' => 14,
            'license_number' => 'MR21722501010432',
        ]);

        User::create([
            'id' => 1155,
            'name' => 'dr. Rahel Permata Herni Simanjuntak',
            'email' => 'doctor1155@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1155,
            'admin_id' => 1,
            'outlet_id' => 18,
            'license_number' => 'MR21722505001894',
        ]);

        User::create([
            'id' => 1156,
            'name' => 'dr.Ayesha Belitania Gamayanti',
            'email' => 'doctor1156@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1156,
            'admin_id' => 1,
            'outlet_id' => 15,
            'license_number' => 'MR21712505009748',
        ]);

        User::create([
            'id' => 1157,
            'name' => 'drg. PIKI PADILAH PURNAMA SIDDIK',
            'email' => 'doctor1157@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1157,
            'admin_id' => 1,
            'outlet_id' => 8,
            'license_number' => 'MR21712504009577',
        ]);

        User::create([
            'id' => 1158,
            'name' => 'drg. PEGGY HABRIKA',
            'email' => 'doctor1158@example.com',
            'password' => Hash::make('dx123'),
            'role_type' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => 1158,
            'admin_id' => 1,
            'outlet_id' => 8,
            'license_number' => 'MR21712503009191',
        ]);

    }
}