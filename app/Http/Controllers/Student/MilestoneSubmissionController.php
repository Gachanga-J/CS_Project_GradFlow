<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\MilestoneSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneSubmissionController extends Controller
{
    public function show(Milestone $milestone)
    {
        $user = Auth::user();

        abort_if($milestone->department_id !== $user->department_id, 403);

        // Auto-mark any unread notifications about this specific milestone as read
        $user->unreadNotifications()
            ->whereJsonContains('data->milestone_id', $milestone->id)
            ->get()
            ->each(function ($notification) {
                $notification->markAsRead();
            });

        $student = $user->student;

        $submission = MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->where('is_latest', true)
            ->first();

        // All versions for history
        $submissionHistory = MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->orderBy('version_number', 'desc')
            ->get();

        return view('student.milestones.show', compact('milestone', 'submission', 'submissionHistory'));
    }

    public function store(Request $request, Milestone $milestone)
    {
        $user = Auth::user();

        abort_if($milestone->department_id !== $user->department_id, 403);
        abort_if($milestone->status === 'closed', 403, 'This milestone is closed.');

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $student = $user->student;

        $latestVersion = MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->max('version_number') ?? 0;

        MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->update(['is_latest' => false]);

        $file     = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs(
            'milestone_submissions/' . $milestone->id . '/' . $student->id,
            time() . '_' . $fileName,
            'local'
        );

        MilestoneSubmission::create([
            'milestone_id'    => $milestone->id,
            'student_id'      => $student->id,
            'file_path'       => $filePath,
            'file_name'       => $fileName,
            'file_size_bytes' => $file->getSize(),
            'mime_type'       => $file->getMimeType(),
            'version_number'  => $latestVersion + 1,
            'is_latest'       => true,
            'status'          => 'submitted',
            'submitted_at'    => now(),
        ]);

        return redirect()->route('student.milestones.show', $milestone)
            ->with('success', 'Submission uploaded successfully!');
    }
}
