<style type="text/css">
    div {
        width: 450px;
        padding: 40px;
        margin: 5px;
        margin-top: -30px;
    }

    .tg {
        border-collapse: collapse;
        border-spacing: 0;
    }

    .tg td {
        border-color: black;
        border-style: solid;
        border-width: 1px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }

    .tg th {
        border-color: black;
        border-style: solid;
        border-width: 1px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }

    .tg .tg-73oq {
        border-color: #000000;
        text-align: left;
        vertical-align: top
    }

    .tg .tg-0lax {
        text-align: left;
        vertical-align: top
    }
</style>
<img src="{{ public_path('assets/logo/KF_LK_Logo_Full_Colors_Primer_Tagline.png') }}" alt="" height="100px;"
    style="margin-left:500px;margin-top:-40px;">
<div>
    <table style="undefined;table-layout: fixed; width: 620px;margin-top:-30px;">
        <h4 style="text-align: center;margin-top:-30px;">SURAT KETERANGAN ISTIRAHAT</h4>
        <h4 style="text-align: center;font-style:italic;margin-top:-20px;font-weight: normal;">MEDICAL CERTIFICATE
        </h4>
        <h4 style="text-align: center;margin-top:-10px;font-weight: normal;">{{ $result->no_letters ?? '-' }}</h4>
        <h4 style="margin-top:-20px; font-weight: normal; text-align:justify;">
            Yang bertanda tangan di bawah ini, Dokter Klinik Kimia Farma, menerangkan bahwa:
        </h4>
        <h4 style="margin-top:-20px; font-style: italic; font-weight: normal; text-align:justify;">
            The undersigned, a doctor at Kimia Farma Clinic, hereby declares that:
        </h4>
    </table>
</div>

<table style="undefined;table-layout: fixed; width: 620px; margin-left: 47px; margin-top: -48px;">
        <colgroup>
            <col style="width: 188px">
            <col style="width: 23px">
            <col style="width: 273px">
        </colgroup>
        <tbody>
            <tr style="margin-bottom:100px;">
                <td style="width:15%;">Nama<br><span style="font-style:italic">Name</span></td>
                <td style="width:2%">:</td>
                <td style="width:65%;font-weight:bold;">{{ $result->patient->full_name }}</td>
            </tr>
            <span style="margin-bottom: 20px;"></span>
           <tr>
                <td>
                    Tanggal Lahir<br>
                    <span style="font-style:italic">Date of Birth</span>
                </td>
                <td style="width:2%">:</td>
                <td style="width:65%; font-weight:bold;">
                    @php
                        $dob = \Carbon\Carbon::parse($result->patient->birth_date);
                        $age = $dob->age;
                    @endphp
                    {{ $dob->translatedFormat('d F Y') }} ({{ $age }} thn)<br>
                    <span style="font-style:italic">
                    </span>
                </td>
            </tr>
            <span style="margin-bottom: 20px;"></span>
            <tr>
                <td>Instansi<br>
                    <span style="font-style:italic">Company</span>
                </td>
                <td style="width:2%">:</td>
                <td style="width:65%;font-weight:bold;">
                    {{ $result->company->name ?? '-' }}
                </td>
            </tr>
            <span style="margin-bottom: 20px;"></span>
            <tr>
                <td>No. Pegawai<br>
                    <span style="font-style:italic">Badge</span>
                </td>
                <td style="width:2%">:</td>
                <td style="width:65%;font-weight:bold;">{{ $result->patient->identity ?? '-' }}</td>
            </tr>
            <span style="margin-bottom: 20px;"></span>
            <tr>
                <td>Alamat<br>
                    <span style="font-style:italic">Address</span>
                </td>
                <td style="width:2%">:</td>
                <td style="width:65%;font-weight:bold;">{{ $result->patient->address }}</td>
            </tr>
            <span style="margin-bottom: 20px;"></span>
            <tr>
                <td>No Telep/HP<br>
                    <span style="font-style:italic">Phone Number</span>
                </td>
                <td style="width:2%">:</td>
                <td style="width:65%;font-weight:bold;">{{ $result->patient->phone }}</td>
            </tr>
            <tr>
                <td>Diagnosa<br>
                    <span style="font-style:italic">Diagnosis</span>
                </td>
                <td style="width:2%">:</td>
                <td style="width:65%;font-weight:bold;">{{ $result->diagnosis->diagnosis_name ?? '-' }}</td>
            </tr>
        </tbody>
    </table>
    <table style="undefined;table-layout: fixed; width: 620px; margin-left: 47px; margin-top: 40px;">
        <h4 style="margin-top:-20px;font-weight: normal;text-align:justify;">Berdasarkan pemeriksaan medis yang telah dilakukan, yang bersangkutan diketahui menderita sakit dan memerlukan istirahat selama:</h4>
        <h4 style="font-style:italic;margin-top:-20px;font-weight: normal;text-align:justify;">Based on the medical examination conducted, the individual is diagnosed with an illness and requires a period of medical leave for:</h4>
    </table>
    <table class="tg" style="undefined;table-layout: fixed; width: 620px; margin-left: 47px; margin-top: 20px;">
        <colgroup>
            <col style="width: 21.67%;">
            <col style="width: 21.67%;">
            <col style="width: 21.66%;">
            <col style="width: 35%;">
        </colgroup>
        <thead>
            <tr>
                <th class="tg-73oq" style="text-align: center;width:13%;"><span
                        style="font-weight:bold">Durasi</span><br><span style="font-style:italic">Duration</span></th>
                <th class="tg-0lax" style="text-align: center;width:20%;"><span style="font-weight:bold">Tanggal
                        Mulai</span><br><span style="font-style:italic">Start From</span></th>
                <th class="tg-0lax" style="text-align: center;width:20%;"><span style="font-weight:bold">Tanggal
                        Selesai</span><br><span style="font-style:italic">Until Date</span></th>
                <th class="tg-0lax" style="text-align: center;"><span style="font-weight:bold">Klinik</span><br><span
                        style="font-style:italic">Healthcare Facility</span></th>
            </tr>
        </thead>
        <tbody>
            @php
                use Carbon\Carbon;

                $tanggal = $result->start_date ? Carbon::parse($result->start_date) : null;
                $hariIDStart = $tanggal ? $tanggal->translatedFormat('l') : '-';
                $hariENStart = $tanggal ? $tanggal->locale('en')->translatedFormat('l') : '-';
                $tanggalIDStart = $tanggal ? $tanggal->translatedFormat('d F Y') : '-';
                $tanggalENStart = $tanggal ? $tanggal->locale('en')->translatedFormat('F jS Y') : '-';

                $tanggal2 = $result->end_date ? Carbon::parse($result->end_date) : null;
                $hariIDend = $tanggal2 ? $tanggal2->translatedFormat('l') : '-';
                $hariENend = $tanggal2 ? $tanggal2->locale('en')->translatedFormat('l') : '-';
                $tanggalIDend = $tanggal2 ? $tanggal2->translatedFormat('d F Y') : '-';
                $tanggalENend = $tanggal2 ? $tanggal2->locale('en')->translatedFormat('F jS Y') : '-';

                $tanggal1 = $result->created_at ? Carbon::parse($result->created_at) : null;
                $hariID = $tanggal1 ? $tanggal1->translatedFormat('l') : '-';
                $hariEN = $tanggal1 ? $tanggal1->locale('en')->translatedFormat('l') : '-';
                $tanggalID = $tanggal1 ? $tanggal1->translatedFormat('d F Y') : '-';
                $tanggalEN = $tanggal1 ? $tanggal1->locale('en')->translatedFormat('F jS Y') : '-';
            @endphp
            <tr>
                <td class="tg-73oq" style="text-align: center;">
                    {{ $result->duration }} Hari<br>
                    <span style="font-style:italic"> {{ $result->duration }} days</span>
                </td>
                <td class="tg-0lax" style="text-align: center;">
                    {{ $tanggalIDStart }}<br>
                    <span style="font-style:italic">{{ $tanggalENStart }}</span>
                </td>
                <td class="tg-0lax" style="text-align: center;">
                    {{ $tanggalIDend }}<br>
                    <span style="font-style:italic">{{ $tanggalENend }}</span>
                </td>
                <td class="tg-0lax" style="text-align: center;">
                    {{ $result->outlet->name }}
                </td>
            </tr>
        </tbody>
    </table>
    <table style="undefined;table-layout: fixed; width: 620px; margin-left: 47px; margin-top: 40px;">
        <h4 style="margin-top:-20px;font-weight: normal;text-align:justify;">Demikian disampaikan agar pihak yang berkepentingan maklum, dan kepada yang bersangkutan agar dapat digunakan sebagaimana mestinya.</h4>
        <h4 style="font-style:italic;margin-top:-20px;font-weight: normal;text-align:justify;">Accordingly, this letter is issued for proper use by the concerned party.</h4>
    </table>
    <br>
    <table style="undefined;table-layout: fixed; width: 620px ;margin-left: 47px">
        <h4 style="margin-top:-20px;font-weight: normal;text-align:left;">{{ $result->outlet->city ?? '-' }},
            {{ $tanggalID ?? '-' }}</h4>
        <h4 style="margin-top:-20px;font-weight: normal;text-align:left;font-style:italic;">
            {{ $result->outlet->city ?? '-' }},
            {{ $tanggalEN ?? '-' }}</h4>
    </table>
     @if ($result->sign_type == 'sign_virtual')
        <div style="position: relative;top:0;left:0;margin-top:10px;">
            <img src="{{ public_path('assets/logo/KF_LK_Logo_Full_Colors_Primer_Tagline.png') }}" alt="" height="70px;"
                style="margin-top: -30px; margin-left: 90px; position: relative;
                top: 0;
                left: -100px;">
            <img src="{{ Storage::disk('public')->url($result->sign_value) }}"
                style="
                height:70px;width:70px;object-fit:cover;
                margin-top: -100px; position: absolute; margin-left: 50px;
                top: 100px;
                left: -10px;">
        </div>
    @elseif ($result->sign_type == 'qrcode')
        <img src="data:image/svg+xml;base64,'{{ base64_encode(QrCode::size(100)->generate($result->sign_value)) }}"
            width="100" height="100" style="margin-bottom:20px" />
    @else
        <img src="{{ public_path('assets/logo/KF_LK_Logo_Full_Colors_Primer_Tagline.png') }}" alt="" height="70px;"
            style="margin-top: -30px;margin-left:90px">
    @endif

    <table style="undefined;table-layout: fixed; width: 620px; margin-left: 47px">
        <h4 style="margin-top:-20px;font-weight: lighter;text-align:left;font-style:italic;position:absolute;">
            <i>{{ $result->doctor?->user?->name ?? $result->doctor?->name ?? '-' }}</i>
        </h4>
        <h5 style="margin-top: 5px;font-weight: lighter;text-align:left;position:absolute;">
            <small></small>: {{ $result->doctor->lisense_number }}</h5>
    </table>
    
    <div style="margin-top:-130px;margin-left:530px;position:absolute;z-index:100;">
        @php
            use App\Helpers\QrEncryptHelper;
            $encrypted = QrEncryptHelper::encrypt($result->unique_code);
            $verifyUrl = route('result.verify', ['code' => $encrypted]);
        @endphp

        <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(100)->generate($verifyUrl)) }}"
            width="100" height="100" alt="QR Code Validasi" />
        <p style="text-align: left; margin-top: 10px; font-size: 10px;">Pindai untuk Verifikasi <br> Keaslian Dokumen</p>
        <p style="text-align: left; margin-top: -15px; font-style: italic; font-size: 10px;">Scan to verify the authenticity <br> of this document</p>
    </div>
    <div style="margin-top: 10;margin-left: 16px;position:absolute;">
        <h5 style="color:#211c5e">{{ $result->outlet->name }}</h5>
    </div>
    <div style="margin-top: 55px;margin-left: 16px;position:absolute;width: 720px">
        <small>
            <small>
                <span style="color:coral;"><b>A:</b></span>
                <span style="font-weight: lighter;">{{ $result->outlet->address ?? '-' }}</span> |
                
                <span style="color:coral;"><b>P:</b></span>
                <span style="font-weight: lighter;">{{ $result->outlet->phone ?? '-' }}</span> |
                
                <span style="color:coral;"><b>E:</b></span>
                <span style="font-weight: lighter;">
                    {{ optional($result->outlet?->user)->email ?? '-' }}
                </span>
            </small>
    </div>
    <img src="{{ public_path('assets/logo/icons.png') }}" alt="" height="450px;"style="margin-left:630px;margin-top:-310px;position:absolute;">
  

