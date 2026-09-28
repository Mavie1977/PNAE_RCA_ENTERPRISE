<?php
namespace App\Http\Controllers;
use App\Models\Application;
use App\Services\AuditService;

class CitizenController extends Controller { public function dashboard(){ $applications=Application::with('procedure')->where('user_id',auth()->id())->latest()->get(); return view('citizen.dashboard',compact('applications')); } }
