<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { display: flex; padding: 10px 0; border-bottom: 1px solid #eee; }
        .detail-label { font-weight: bold; width: 150px; color: #666; }
        .detail-value { flex: 1; }
        .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Booking Confirmed!</h1>
            <p>Your MCU booking has been successfully confirmed</p>
        </div>

        <div class="content">
            <p>Dear {{ $booking->patient_name }},</p>

            <p>Thank you for your booking! Your medical check-up appointment has been confirmed. Please find the details below:</p>

            <div class="booking-details">
                <h3 style="margin-top: 0; color: #667eea;">Booking Details</h3>

                <div class="detail-row">
                    <div class="detail-label">Booking Number:</div>
                    <div class="detail-value"><strong>{{ $booking->booking_number }}</strong></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Package:</div>
                    <div class="detail-value">{{ $package->name }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Provider:</div>
                    <div class="detail-value">{{ $provider->name }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Location:</div>
                    <div class="detail-value">{{ $provider->address }}, {{ $provider->city }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Date:</div>
                    <div class="detail-value">{{ $booking->booking_date->format('l, d F Y') }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Time:</div>
                    <div class="detail-value">{{ $booking->booking_time->format('H:i') }} WIB</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Total Paid:</div>
                    <div class="detail-value"><strong>Rp {{ number_format($booking->final_price, 0, ',', '.') }}</strong></div>
                </div>
            </div>

            <h3>Important Information:</h3>
            <ul>
                <li><strong>Fasting Required:</strong> Please fast for 10-12 hours before the check-up</li>
                <li><strong>Arrive Early:</strong> Please arrive 15 minutes before your scheduled time</li>
                <li><strong>Bring ID:</strong> Please bring your ID card (KTP/Passport)</li>
                <li><strong>Bring This Email:</strong> Show this confirmation email at the registration desk</li>
            </ul>

            @if($package->preparation_instructions && count($package->preparation_instructions) > 0)
            <h3>Preparation Instructions:</h3>
            <ul>
                @foreach($package->preparation_instructions as $instruction)
                <li>{{ $instruction }}</li>
                @endforeach
            </ul>
            @endif

            <div style="text-align: center;">
                <a href="{{ route('mcu.bookings.show', $booking) }}" class="button">
                    View Booking Details
                </a>
            </div>

            <p>If you have any questions or need to reschedule, please contact us at:</p>
            <p>
                <strong>Phone:</strong> {{ $provider->phone }}<br>
                <strong>Email:</strong> {{ $provider->email }}
            </p>

            <p>We look forward to serving you!</p>

            <p>Best regards,<br>
            <strong>{{ $provider->name }}</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply to this email.</p>
            <p>© 2025 MCv3. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
