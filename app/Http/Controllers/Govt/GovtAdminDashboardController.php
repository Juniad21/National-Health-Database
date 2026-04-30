<?php

namespace App\Http\Controllers\Govt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GovtAdminDashboardController extends Controller
{
    public function index()
    {
        // Mock data for the dashboard
        $stats = [
            'registered_doctors' => 1240,
            'pending_verifications' => 85,
            'registered_hospitals' => 120,
            'hospitals_under_review' => 12,
            'active_patients' => '4.2M',
            'claims_processed' => '850K',
            'reported_incidents' => 24
        ];

        $pendingDoctors = [
            ['name' => 'Dr. Arif Rahman', 'license' => 'BMDC-8923', 'specialty' => 'Cardiology', 'hospital' => 'Square Hospital', 'date' => '2026-04-28', 'status' => 'Pending'],
            ['name' => 'Dr. Nusrat Jahan', 'license' => 'BMDC-4567', 'specialty' => 'Pediatrics', 'hospital' => 'Evercare Hospital', 'date' => '2026-04-29', 'status' => 'Needs Review'],
            ['name' => 'Dr. Kamal Hossain', 'license' => 'BMDC-1234', 'specialty' => 'Neurology', 'hospital' => 'United Hospital', 'date' => '2026-04-27', 'status' => 'Pending'],
        ];

        $hospitals = [
            ['name' => 'Square Hospital', 'district' => 'Dhaka', 'type' => 'Private', 'beds' => '85%', 'icu' => '2/20', 'vent' => '5/10', 'blood' => 'Normal', 'compliance' => 'Normal'],
            ['name' => 'Evercare Hospital', 'district' => 'Dhaka', 'type' => 'Private', 'beds' => '92%', 'icu' => '0/15', 'vent' => '1/8', 'blood' => 'Warning', 'compliance' => 'Warning'],
            ['name' => 'DMCH', 'district' => 'Dhaka', 'type' => 'Government', 'beds' => '98%', 'icu' => '0/50', 'vent' => '0/20', 'blood' => 'Critical', 'compliance' => 'Normal'],
        ];

        $alerts = [
            ['title' => 'Critical ICU Shortage', 'target' => 'Evercare Hospital', 'severity' => 'Critical', 'time' => '10 mins ago'],
            ['title' => 'Low O- Blood Stock', 'target' => 'DMCH', 'severity' => 'High', 'time' => '1 hour ago'],
            ['title' => 'Expired License Detected', 'target' => 'Dr. Rahim (BMDC-0012)', 'severity' => 'Medium', 'time' => '3 hours ago'],
        ];

        return view('govt_admin.dashboard', compact('stats', 'pendingDoctors', 'hospitals', 'alerts'));
    }
}
