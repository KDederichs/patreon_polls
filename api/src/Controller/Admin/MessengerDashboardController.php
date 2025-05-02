<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Attribute\Route;
use Zenstruck\Messenger\Monitor\Controller\MessengerMonitorController as BaseMessengerMonitorController;

#[Route('/admin/messenger')] // path prefix for the controllers
class MessengerDashboardController extends BaseMessengerMonitorController
{

}
