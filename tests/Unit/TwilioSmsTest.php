<?php

namespace Tests\Unit;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Modules\Sms\Channels\TwilioChannel;
use Modules\Sms\Entities\SmsGlobalSetting;
use Modules\Sms\Messages\TwilioSmsMessage;
use Modules\Sms\Services\TwilioSmsClient;
use RuntimeException;
use Tests\TestCase;

class TwilioSmsTest extends TestCase
{
    public function test_twilio_sms_client_posts_to_messages_api_with_basic_auth(): void
    {
        Http::fake([
            'https://api.twilio.com/2010-04-01/Accounts/ACtest123/Messages.json' => Http::response([
                'sid' => 'SM1234567890abcdef',
                'status' => 'queued',
            ], 201),
        ]);

        $sid = TwilioSmsClient::send(
            'ACtest123',
            'secret_token',
            '+15551234567',
            '+919876543210',
            'Hello from TableTrack'
        );

        $this->assertSame('SM1234567890abcdef', $sid);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.twilio.com/2010-04-01/Accounts/ACtest123/Messages.json'
                && $request->hasHeader('Authorization')
                && $request['From'] === '+15551234567'
                && $request['To'] === '+919876543210'
                && $request['Body'] === 'Hello from TableTrack';
        });
    }

    public function test_twilio_sms_client_throws_on_api_error(): void
    {
        Http::fake([
            'https://api.twilio.com/*' => Http::response([
                'message' => 'Authenticate',
                'code' => 20003,
            ], 401),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Twilio SMS failed: 401');

        TwilioSmsClient::send('ACbad', 'bad', '+1', '+1', 'test');
    }

    public function test_twilio_channel_sends_notification_using_global_settings(): void
    {
        Http::fake([
            'https://api.twilio.com/*' => Http::response(['sid' => 'SMchanneltest'], 201),
        ]);

        $setting = new SmsGlobalSetting([
            'twilio_account_sid' => 'ACchannel',
            'twilio_auth_token' => 'token_channel',
            'twilio_from_number' => '+15550001111',
            'twilio_status' => true,
        ]);

        $channel = new class($setting) extends TwilioChannel
        {
            public function __construct(private SmsGlobalSetting $settings) {}

            protected function resolveSettings(): ?SmsGlobalSetting
            {
                return $this->settings;
            }
        };

        $notifiable = new class
        {
            public string $phone_code = '91';

            public string $phone_number = '9876543210';

            public function routeNotificationFor($channel, $notification = null)
            {
                return null;
            }
        };

        $notification = new class extends Notification
        {
            public function toTwilio($notifiable): TwilioSmsMessage
            {
                return (new TwilioSmsMessage)->content('OTP 1234');
            }
        };

        $channel->send($notifiable, $notification);

        Http::assertSent(function ($request) {
            return $request['From'] === '+15550001111'
                && $request['To'] === '+919876543210'
                && $request['Body'] === 'OTP 1234';
        });
    }
}
