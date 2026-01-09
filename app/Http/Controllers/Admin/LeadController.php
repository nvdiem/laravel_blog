<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class LeadController extends Controller
{
    /**
     * Display a listing of leads.
     */
    public function index()
    {
        $leads = Lead::with('page')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.leads.index', compact('leads'));
    }

    /**
     * Export leads to CSV.
     */
    public function export()
    {
        $leads = Lead::with('page')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'leads_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($leads) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Page',
                'Name',
                'Email',
                'Phone',
                'Payload',
                'Submitted At'
            ]);

            // CSV data
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->page->title ?? 'Unknown Page',
                    $lead->name ?? '',
                    $lead->email ?? '',
                    $lead->phone ?? '',
                    json_encode($lead->payload),
                    $lead->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
