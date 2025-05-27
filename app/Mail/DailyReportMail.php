<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    public function build()
    {
        return $this->markdown('emails.daily-report')
            ->subject('Daily Email Report - ' . $this->reportData['date'])
            ->with([
                'date' => $this->reportData['date'],
                'stats' => $this->reportData['stats']
            ]);
    }
}
