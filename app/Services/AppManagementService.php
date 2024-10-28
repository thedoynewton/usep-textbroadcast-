<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Employee;
use App\Models\MessageTemplate;
use App\Models\Campus;

class AppManagementService
{
    public function getStudents($search = null, $campusId = null, $paginate = true)
    {
        $query = Student::with('campus')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('stud_fname', 'like', "%{$search}%")
                        ->orWhere('stud_lname', 'like', "%{$search}%")
                        ->orWhere('stud_email', 'like', "%{$search}%")
                        ->orWhere('stud_contact', 'like', "%{$search}%");
                });
            })
            ->when($campusId, function ($query, $campusId) {
                return $query->where('campus_id', $campusId);
            });

        return $paginate ? $query->paginate(10) : $query->get();
    }

    public function getEmployees($search = null, $campusId = null, $paginate = true)
    {
        $query = Employee::with('campus')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('emp_fname', 'like', "%{$search}%")
                        ->orWhere('emp_lname', 'like', "%{$search}%")
                        ->orWhere('emp_email', 'like', "%{$search}%")
                        ->orWhere('emp_contact', 'like', "%{$search}%");
                });
            })
            ->when($campusId, function ($query, $campusId) {
                return $query->where('campus_id', $campusId);
            });

        return $paginate ? $query->paginate(10) : $query->get();
    }
    public function getContacts($search = null, $campusId = null, $paginate = true)
    {
        $students = $this->getStudents($search, $campusId, false); // Get students without pagination
        $employees = $this->getEmployees($search, $campusId, false); // Get employees without pagination
    
        // Concatenate students and employees into a single collection
        $contacts = $students->concat($employees);
    
        // Sort and paginate if needed
        if ($paginate) {
            $perPage = 10;
            $currentPage = request()->get('page', 1);
            $contacts = $contacts->sortBy('stud_lname')->values();
            $contacts = $contacts->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $contacts = new \Illuminate\Pagination\LengthAwarePaginator(
                $contacts,
                $students->count() + $employees->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }
    
        return $contacts;
    }    
    
    public function getMessageTemplates()
    {
        return MessageTemplate::paginate(10);
    }

    public function getTotalCounts()
    {
        return [
            'totalStudents' => Student::count(),
            'totalEmployees' => Employee::count(),
        ];
    }

    public function getCampuses()
    {
        return Campus::all();
    }
}
