<?php

namespace App\Jobs;

use App\Mail\QuoteReceivedMail;
use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;



class SendQuoteNotificationJob implements ShouldQueue 
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public int $tries = 3;
    public function __construct(public QuoteRequest $quote){}


    public function handle(): void 
    {
        if ($reason = $this->spamReason()) {
            $this->quote->update([
                'status'      => 'spam',
                'spam_reason' => $reason,
            ]);

            Log::info('Quote marked as spam', [
                'reference' => $this->quote->reference,
                'reason'    => $reason,
            ]);

            return;
        }


         Mail::to(config('sales.notification_email'))
            ->send(new QuoteReceivedMail($this->quote));

        $this->quote->update([
            'status'     => 'reviewing',
            'emailed_at' => now(),
        ]);

    }

    protected function spamReason(): ?string
    {
        $config = config('sales.spam');

        if (! empty($this->quote->details) && str_contains(strtolower($this->quote->details), 'http')) {
            $urlCount = preg_match_all('/https?:\/\//i', $this->quote->details);
            if ($urlCount > $config['max_urls_in_details']) {
                return "Too many URLs in details ({$urlCount})";
            }
        }

        $domain = strtolower(substr(strrchr($this->quote->email, '@') ?: '', 1));
        if (in_array($domain, $config['blocked_domains'], true)) {
            return "Disposable email domain: {$domain}";
        }

        if ($this->quote->ip_address) {
            $recent = QuoteRequest::query()
                ->where('ip_address', $this->quote->ip_address)
                ->where('created_at', '>=', now()->subHour())
                ->whereKeyNot($this->quote->id)
                ->count();

            if ($recent >= $config['max_per_ip_per_hour']) {
                return "Rate limit: {$recent} quotes from same IP in last hour";
            }
        }

        return null;
    }
}