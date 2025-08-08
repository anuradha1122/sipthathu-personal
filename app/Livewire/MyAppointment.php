<?php

namespace App\Livewire;

use Livewire\Component;

class MyAppointment extends Component
{

    public $cryptedId;
    public $service;
    public $appointment;
    public $position;
    public $previousServices;
    public $currentService;
    public $previousServiceRanks;
    public $currentServiceRanks;
    public $previousAppointments;
    public $currentAppointments;
    public $previousAttachAppointments;
    public $currentAttachAppointment;

    public function render()
    {
        return view('livewire.my-appointment');
    }
}
