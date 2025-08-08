<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\UserInService;
use App\Models\UserServiceAppointment;
use App\Models\UserServiceAppointmentPosition;
use App\Models\EducationQualification;
use App\Models\ProfessionalQualification;
use App\Models\FamilyInfo;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }


    public function myprofile()
    {

        if(Auth::user()->id){
            try{

                $option = [
                    'Dashboard' => 'dashboard',
                    'My Profile' => route('profile.myprofile'),
                ];

                //$decryptedId = Crypt::decryptString($request->id);
                //dd($decryptedId);
                $user = User::leftjoin('personal_infos', 'users.id', '=', 'personal_infos.userId')
                ->leftjoin('races', 'personal_infos.raceId', '=', 'races.id')
                ->leftjoin('religions', 'personal_infos.religionId', '=', 'religions.id')
                ->leftjoin('civil_statuses', 'personal_infos.civilStatusId', '=', 'civil_statuses.id')
                ->leftjoin('contact_infos', 'users.id', '=', 'contact_infos.userId')
                ->leftjoin('location_infos', 'users.id', '=', 'location_infos.userId')
                //->leftjoin('offices', 'location_infos.educationDivisionId', '=', 'offices.id')
                ->leftjoin('work_places AS educationDivisions', 'location_infos.educationDivisionId', '=', 'educationDivisions.id')
                ->leftjoin('gn_divisions', 'location_infos.gnDivisionId', '=', 'gn_divisions.id')
                ->leftjoin('ds_divisions', 'gn_divisions.dsId', '=', 'ds_divisions.id')
                ->leftjoin('districts', 'ds_divisions.districtId', '=', 'districts.id')
                ->leftjoin('provinces', 'districts.provinceId', '=', 'provinces.id')
                ->where('users.id', Auth::user()->id)
                ->select(
                    'users.id AS userId','users.name AS name','users.nic','users.email','users.nameWithInitials',
                    'personal_infos.birthDay','personal_infos.profilePicture',
                    DB::raw("CASE
                        WHEN personal_infos.genderId = 1 THEN 'Male'
                        WHEN personal_infos.genderId = 2 THEN 'Female'
                        ELSE 'Unknown'
                    END AS gender"),
                    'races.name AS race',
                    'religions.name AS religion',
                    'civil_statuses.name AS civilStatus',
                    'contact_infos.*',
                    'educationDivisions.name AS educationDivision',
                    'gn_divisions.name AS gnDivision',
                    'ds_divisions.name AS dsDivision',
                    'districts.name AS district',
                    'provinces.name AS province',
                )
                ->first();
                    //dd($user);
                $combinedData = UserInService::join('services', 'user_in_services.serviceId', '=', 'services.id')
                    ->leftJoin('user_service_in_ranks', 'user_in_services.id', '=', 'user_service_in_ranks.userServiceId')
                    ->leftJoin('ranks', 'user_service_in_ranks.rankId', '=', 'ranks.id')
                    ->where('user_in_services.userId', Auth::user()->id)
                    ->select(
                        'user_in_services.id AS userServiceId',
                        'user_in_services.appointedDate',
                        'user_in_services.releasedDate',
                        'user_in_services.current AS currentService',
                        'services.name AS serviceName',
                        'user_service_in_ranks.id AS serviceRankId',
                        'user_service_in_ranks.rankId',
                        'user_service_in_ranks.rankedDate',
                        'user_service_in_ranks.current AS currentRank',
                        'ranks.name AS rank'
                    )
                    ->get();

                // Partition services into current and previous
                $partitionedData = $combinedData->partition(function ($item) {
                    return $item->currentService == 1 && is_null($item->releasedDate);
                });

                // Get distinct current services (no ranks)
                $currentService = $partitionedData[0]
                    ->unique('userServiceId')
                    ->map(function ($item) {
                        $servicePeriod = "from {$item->appointedDate} to " . ($item->releasedDate ?? 'present');
                        return [
                            'userServiceId' => $item->userServiceId,
                            'formattedService' => "{$item->serviceName} {$servicePeriod}",
                        ];
                    }); // Keep as a collection

                // Extract current service IDs
                $currentServiceIds = $currentService->pluck('userServiceId');

                // If you need to convert $currentService to an array for Blade:
                $currentServiceArray = $currentService->pluck('formattedService', 'userServiceId')->toArray();


                $previousServices = $partitionedData[1]
                ->unique('userServiceId')
                ->map(function ($item) {
                    $servicePeriod = "from {$item->appointedDate} to " . ($item->releasedDate ?? 'present');
                    return [
                        'userServiceId' => $item->userServiceId,
                        'formattedService' => "{$item->serviceName} {$servicePeriod}",
                    ];
                }); // Keep as a collection

                $previousServiceIds = $previousServices->pluck('userServiceId');

                // If you need to convert $previousServices to an array for Blade:
                $previousServicesArray = $previousServices->pluck('formattedService', 'userServiceId')->toArray();


                $currentServiceRanks = $combinedData->filter(function ($item) use ($currentServiceIds) {
                    return $currentServiceIds->contains($item->userServiceId) && !is_null($item->serviceRankId);
                })->map(function ($item) {
                    $rankPeriod = "from {$item->rankedDate}";
                    return [
                        'userServiceId' => $item->userServiceId,
                        'formattedRank' => "{$item->rank} {$rankPeriod}",
                    ];
                });

                // Convert to an array for Blade if needed
                $currentServiceRanksArray = $currentServiceRanks->pluck('formattedRank', 'userServiceId')->toArray();

                $previousServiceRanks = $combinedData->filter(function ($item) use ($previousServiceIds) {
                    return $previousServiceIds->contains($item->userServiceId) && !is_null($item->serviceRankId);
                })->map(function ($item) {
                    $rankPeriod = "from {$item->rankedDate}";
                    return [
                        'userServiceId' => $item->userServiceId,
                        'formattedRank' => "{$item->rank} {$rankPeriod}",
                    ];
                });

                // Convert to an array for Blade if needed
                $previousServiceRanksArray = $previousServiceRanks->pluck('formattedRank', 'userServiceId')->toArray();


                // Fetch appointments and categorize them into current and previous based on the service IDs
                $appointments = UserServiceAppointment::join('work_places', 'user_service_appointments.workPlaceId', '=', 'work_places.id')
                ->whereIn('user_service_appointments.userServiceId', $currentServiceIds)
                ->orWhereIn('user_service_appointments.userServiceId', $previousServiceIds)
                ->select(
                    'user_service_appointments.*',
                    'work_places.name AS workPlaceName',
                    'work_places.censusNo AS censusNo',
                    'work_places.categoryId AS workPlaceCategory'
                )
                ->get();

                // Partition appointments into categories based on their attributes
                $appointmentsPartitioned = $appointments->groupBy(function ($appointment) {
                if ($appointment->current == 1 && is_null($appointment->releasedDate)) {
                    return $appointment->appointmentType == 1 ? 'currentAppointments' : 'currentAttachAppointments';
                } elseif ($appointment->current == 0 && !is_null($appointment->releasedDate)) {
                    return $appointment->appointmentType == 1 ? 'previousAppointments' : 'previousAttachAppointments';
                }
                return null; // Ignore other cases
                });

                // Map the partitions to IDs
                $currentAppointmentIds = $appointmentsPartitioned->get('currentAppointments', collect())->pluck('id')->toArray();
                $previousAppointmentIds = $appointmentsPartitioned->get('previousAppointments', collect())->pluck('id')->toArray();
                $currentAttachAppointmentIds = $appointmentsPartitioned->get('currentAttachAppointments', collect())->pluck('id')->toArray();
                $previousAttachAppointmentIds = $appointmentsPartitioned->get('previousAttachAppointments', collect())->pluck('id')->toArray();

                // Format and return results for each category
                $currentAppointments = $appointmentsPartitioned->get('currentAppointments', collect())
                ->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'formattedAppointment' => "{$appointment->workPlaceName} from {$appointment->appointedDate}",
                    ];
                })->pluck('formattedAppointment', 'id')->toArray();

                $previousAppointments = $appointmentsPartitioned->get('previousAppointments', collect())
                ->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'formattedAppointment' => "{$appointment->workPlaceName} from {$appointment->appointedDate} to {$appointment->releasedDate}",
                    ];
                })->pluck('formattedAppointment', 'id')->toArray();

                $currentAttachAppointments = $appointmentsPartitioned->get('currentAttachAppointments', collect())
                ->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'formattedAppointment' => "{$appointment->workPlaceName} from {$appointment->appointedDate}",
                    ];
                })->pluck('formattedAppointment', 'id')->toArray();

                $previousAttachAppointments = $appointmentsPartitioned->get('previousAttachAppointments', collect())
                ->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'formattedAppointment' => "{$appointment->workPlaceName} from {$appointment->appointedDate} to {$appointment->releasedDate}",
                    ];
                })->pluck('formattedAppointment', 'id')->toArray();


                $positions = UserServiceAppointmentPosition::join('positions', 'user_service_appointment_positions.positionId', '=', 'positions.id')
                    ->whereIn('user_service_appointment_positions.userServiceAppointmentId', array_merge(
                        $currentAppointmentIds,
                        $previousAppointmentIds,
                        $currentAttachAppointmentIds,
                        $previousAttachAppointmentIds
                    ))
                    ->select(
                        'user_service_appointment_positions.*',
                        'positions.name AS position'
                    )
                    ->get();

                // Partition positions into categories based on appointment IDs
                $positionsPartitioned = $positions->groupBy(function ($position) use (
                    $currentAppointmentIds,
                    $previousAppointmentIds,
                    $currentAttachAppointmentIds,
                    $previousAttachAppointmentIds,
                ) {
                    if (in_array($position->userServiceAppointmentId, $currentAppointmentIds)) {
                        return 'currentPositions';
                    } elseif (in_array($position->userServiceAppointmentId, $previousAppointmentIds)) {
                        return 'previousPositions';
                    } elseif (in_array($position->userServiceAppointmentId, $currentAttachAppointmentIds)) {
                        return 'currentAttachPositions';
                    } elseif (in_array($position->userServiceAppointmentId, $previousAttachAppointmentIds)) {
                        return 'previousAttachPositions';
                    }
                    return null; // Ignore other cases
                });

                // Map the partitions to structured data
                $currentPositions = $positionsPartitioned->get('currentPositions', collect())
                    ->map(function ($position) {
                        return [
                            'id' => $position->id,
                            'positionName' => $position->position,
                            'details' => $position->toArray(),
                        ];
                })->values();

                $previousPositions = $positionsPartitioned->get('previousPositions', collect())
                    ->map(function ($position) {
                        return [
                            'id' => $position->id,
                            'positionName' => $position->position,
                            'details' => $position->toArray(),
                        ];
                })->values();

                $currentAttachPositions = $positionsPartitioned->get('currentAttachPositions', collect())
                    ->map(function ($position) {
                        return [
                            'id' => $position->id,
                            'positionName' => $position->position,
                            'details' => $position->toArray(),
                        ];
                })->values();

                $previousAttachPositions = $positionsPartitioned->get('previousAttachPositions', collect())
                    ->map(function ($position) {
                        return [
                            'id' => $position->id,
                            'positionName' => $position->position,
                            'details' => $position->toArray(),
                        ];
                })->values();


                $educationQualifications = EducationQualification::join('education_qualification_infos', 'education_qualification_infos.educationQualificationId', '=', 'education_qualifications.id')
                ->where('education_qualification_infos.userId', Auth::user()->id)
                ->where('education_qualification_infos.active', 1)
                ->where('education_qualifications.active', 1)
                ->selectRaw("GROUP_CONCAT(CONCAT(education_qualifications.name, ' Effective from ', education_qualification_infos.effectiveDate) SEPARATOR '\n') as formattedOutput")
                ->pluck('formattedOutput')
                ->first();


                $professionalQualifications = professionalQualification::join('professional_qualification_infos', 'professional_qualification_infos.professionalQualificationId', '=', 'professional_qualifications.id')
                ->where('professional_qualification_infos.userId', Auth::user()->id)
                ->where('professional_qualification_infos.active', 1)
                ->where('professional_qualifications.active', 1)
                ->selectRaw("GROUP_CONCAT(CONCAT(professional_qualifications.name, ' Effective from ', professional_qualification_infos.effectiveDate) SEPARATOR '\n') as formattedOutput")
                ->pluck('formattedOutput')
                ->first();

                $family = FamilyInfo::join('family_member_types', 'family_infos.memberType', '=', 'family_member_types.id')
                ->where('family_infos.userId', Auth::user()->id)
                ->where('family_infos.active', 1)
                ->selectRaw("GROUP_CONCAT(CONCAT(family_infos.name, ' ( ', family_infos.nic, ' ', family_member_types.name, ' ', family_infos.profession, ' )') SEPARATOR '\n') as formattedOutput")
                ->pluck('formattedOutput')
                ->first();



                //dd($family);
                return view('profile/myprofile', compact(
                    'user',
                    'currentServiceArray',
                    'previousServicesArray',
                    'currentServiceRanksArray',
                    'previousServiceRanksArray',
                    'currentAppointments',
                    'previousAppointments',
                    'currentAttachAppointments',
                    'previousAttachAppointments',
                    'currentPositions',
                    'previousPositions',
                    'currentAttachPositions',
                    'previousAttachPositions',
                    'educationQualifications',
                    'professionalQualifications',
                    'family',
                    'option'
                ));


            }catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // Redirect to the search page or show an error message for invalid ID
                return redirect()->route('teacher.search')->with('error', 'Invalid teacher ID provided.');
            }

        }else{
            return redirect()->route('dashboard');
        }
    }

    public function myprofileedit(Request $request)
    {
        //dd($request->category);
        //dd(Auth::check(), Auth::user(), Auth::id());

        //dd($request->has('category'), $request->input('category'));

        if (Auth::check() && $request->has('category')) {
            try{

                $option = [
                    'Dashboard' => 'dashboard',
                    'My Profile' => route('profile.myprofile'),
                    'My Profile Edit' => htmlspecialchars_decode(route('profile.myprofileedit',['category' => $request->category])),
                ];

                $category = $request->category;

                $user = User::find(Auth::user()->id);

                return view('profile/myprofileedit', compact(
                    'user',
                    'category',
                    'option'
                ));


            }catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // Redirect to the search page or show an error message for invalid ID
                return redirect()->route('profile.myprofile')->with('error', 'Invalid User ID provided.');
            }

        }else{
            //dd('adad');
            return redirect()->route('dashboard');
        }
    }

    public function myprofilestore(UpdateProfileRequest $request)
    {
        $category = $request->input('category');
        if ($category === 'login') {
            $user = auth()->user();
            if (!Hash::check($request->currentPassword, $user->password)) {
                return back()->withErrors(['currentPassword' => 'Current password is incorrect.']);
            }
            $user->password = Hash::make($request->newPassword);
            $user->save();
        }

        return redirect()->back()->with('success', 'Information updated successfully!');
    }

    public function myappointment()
    {
        //dd(session('serviceId'));
        if(Auth::user()->id){
            try{

                $option = [
                    'Dashboard' => 'dashboard',
                    'My Profile' => route('profile.myprofile'),
                ];

                // $user = User::leftjoin('location_infos', 'users.id', '=', 'location_infos.userId')
                // //->leftjoin('offices', 'location_infos.educationDivisionId', '=', 'offices.id')
                // ->leftjoin('work_places AS educationDivisions', 'location_infos.educationDivisionId', '=', 'educationDivisions.id')
                // ->leftjoin('gn_divisions', 'location_infos.gnDivisionId', '=', 'gn_divisions.id')
                // ->leftjoin('ds_divisions', 'gn_divisions.dsId', '=', 'ds_divisions.id')
                // ->leftjoin('districts', 'ds_divisions.districtId', '=', 'districts.id')
                // ->leftjoin('provinces', 'districts.provinceId', '=', 'provinces.id')
                // ->where('users.id', Auth::user()->id)
                // ->select(
                //     'users.id AS userId','users.name AS name','users.nic','users.email','users.nameWithInitials',
                //     'educationDivisions.name AS educationDivision',
                //     'gn_divisions.name AS gnDivision',
                //     'ds_divisions.name AS dsDivision',
                //     'districts.name AS district',
                //     'provinces.name AS province',
                // )
                // ->first();

                $combinedData = UserInService::join('services', 'user_in_services.serviceId', '=', 'services.id')
                ->leftJoin('user_service_in_ranks', 'user_in_services.id', '=', 'user_service_in_ranks.userServiceId')
                ->leftJoin('ranks', 'user_service_in_ranks.rankId', '=', 'ranks.id')
                ->where('user_in_services.userId', Auth::user()->id)
                ->select(
                    'user_in_services.id AS userServiceId',
                    'user_in_services.appointedDate',
                    'user_in_services.releasedDate',
                    'user_in_services.current AS currentService',
                    'services.name AS serviceName',
                    'user_service_in_ranks.id AS serviceRankId',
                    'user_service_in_ranks.rankId',
                    'user_service_in_ranks.rankedDate',
                    'user_service_in_ranks.current AS currentRank',
                    'ranks.name AS rank'
                )
                ->get();

                $services = UserInService::join('services', 'user_in_services.serviceId', '=', 'services.id')
                    ->leftJoin('user_service_in_ranks', 'user_in_services.id', '=', 'user_service_in_ranks.userServiceId')
                    ->leftJoin('ranks', 'user_service_in_ranks.rankId', '=', 'ranks.id')
                    ->where('user_in_services.userId', Auth::user()->id)
                    ->select(
                        'user_in_services.id AS userServiceId',
                        'user_in_services.appointedDate',
                        'user_in_services.releasedDate',
                        'user_in_services.current AS currentService',
                        'services.name AS serviceName',
                        'user_service_in_ranks.id AS serviceRankId',
                        'user_service_in_ranks.rankId',
                        'user_service_in_ranks.rankedDate',
                        'user_service_in_ranks.current AS currentRank',
                        'ranks.name AS rank'
                    )
                    ->get();
                //dd($services);
                // Partition services into current and previous
                $partitionedData = $services->partition(function ($item) {
                    return $item->currentService == 1 && is_null($item->releasedDate);
                });

                // Get distinct current services (no ranks)
                $currentService = $partitionedData[0]
                    ->unique('userServiceId')
                    ->map(function ($item) {
                        $servicePeriod = "from {$item->appointedDate} to " . ($item->releasedDate ?? 'present');
                        return [
                            'userServiceId' => $item->userServiceId,
                            'formattedService' => "{$item->serviceName} {$servicePeriod}",
                        ];
                    }); // Keep as a collection

                // Extract current service IDs
                $currentServiceIds = $currentService->pluck('userServiceId');

                // If you need to convert $currentService to an array for Blade:
                $currentServiceArray = $currentService->pluck('formattedService', 'userServiceId')->toArray();


                $previousServices = $partitionedData[1]
                ->unique('userServiceId')
                ->map(function ($item) {
                    $servicePeriod = "from {$item->appointedDate} to " . ($item->releasedDate ?? 'present');
                    return [
                        'userServiceId' => $item->userServiceId,
                        'formattedService' => "{$item->serviceName} {$servicePeriod}",
                    ];
                }); // Keep as a collection

                $previousServiceIds = $previousServices->pluck('userServiceId');

                // If you need to convert $previousServices to an array for Blade:
                $previousServicesArray = $previousServices->pluck('formattedService', 'userServiceId')->toArray();


                $currentServiceRanks = $combinedData->filter(function ($item) use ($currentServiceIds) {
                    return $currentServiceIds->contains($item->userServiceId) && !is_null($item->serviceRankId);
                })->map(function ($item) {
                    $rankPeriod = "from {$item->rankedDate}";
                    return [
                        'userServiceId' => $item->userServiceId,
                        'formattedRank' => "{$item->rank} {$rankPeriod}",
                    ];
                });

                // Convert to an array for Blade if needed
                $currentServiceRanksArray = $currentServiceRanks->pluck('formattedRank', 'userServiceId')->toArray();

                $previousServiceRanks = $combinedData->filter(function ($item) use ($previousServiceIds) {
                    return $previousServiceIds->contains($item->userServiceId) && !is_null($item->serviceRankId);
                })->map(function ($item) {
                    $rankPeriod = "from {$item->rankedDate}";
                    return [
                        'userServiceId' => $item->userServiceId,
                        'formattedRank' => "{$item->rank} {$rankPeriod}",
                    ];
                });

                // Convert to an array for Blade if needed
                $previousServiceRanksArray = $previousServiceRanks->pluck('formattedRank', 'userServiceId')->toArray();


                // Fetch appointments and categorize them into current and previous based on the service IDs
                $appointments = UserServiceAppointment::join('work_places', 'user_service_appointments.workPlaceId', '=', 'work_places.id')
                ->whereIn('user_service_appointments.userServiceId', $currentServiceIds)
                ->orWhereIn('user_service_appointments.userServiceId', $previousServiceIds)
                ->select(
                    'user_service_appointments.*',
                    'work_places.name AS workPlaceName',
                    'work_places.censusNo AS censusNo',
                    'work_places.categoryId AS workPlaceCategory'
                )
                ->get();

                // Partition appointments into categories based on their attributes
                $appointmentsPartitioned = $appointments->groupBy(function ($appointment) {
                if ($appointment->current == 1 && is_null($appointment->releasedDate)) {
                    return $appointment->appointmentType == 1 ? 'currentAppointments' : 'currentAttachAppointments';
                } elseif ($appointment->current == 0 && !is_null($appointment->releasedDate)) {
                    return $appointment->appointmentType == 1 ? 'previousAppointments' : 'previousAttachAppointments';
                }
                return null; // Ignore other cases
                });

                // Map the partitions to IDs
                $currentAppointmentIds = $appointmentsPartitioned->get('currentAppointments', collect())->pluck('id')->toArray();
                $previousAppointmentIds = $appointmentsPartitioned->get('previousAppointments', collect())->pluck('id')->toArray();
                $currentAttachAppointmentIds = $appointmentsPartitioned->get('currentAttachAppointments', collect())->pluck('id')->toArray();
                $previousAttachAppointmentIds = $appointmentsPartitioned->get('previousAttachAppointments', collect())->pluck('id')->toArray();

                // Format and return results for each category
                $currentAppointments = $appointmentsPartitioned->get('currentAppointments', collect())
                ->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'formattedAppointment' => "{$appointment->workPlaceName} from {$appointment->appointedDate}",
                    ];
                })->pluck('formattedAppointment', 'id')->toArray();

                $previousAppointments = $appointmentsPartitioned->get('previousAppointments', collect())
                ->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'formattedAppointment' => "{$appointment->workPlaceName} from {$appointment->appointedDate} to {$appointment->releasedDate}",
                    ];
                })->pluck('formattedAppointment', 'id')->toArray();
                //dd($previousAppointments);
                $currentAttachAppointments = $appointmentsPartitioned->get('currentAttachAppointments', collect())
                ->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'formattedAppointment' => "{$appointment->workPlaceName} from {$appointment->appointedDate}",
                    ];
                })->pluck('formattedAppointment', 'id')->toArray();

                $previousAttachAppointments = $appointmentsPartitioned->get('previousAttachAppointments', collect())
                ->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'formattedAppointment' => "{$appointment->workPlaceName} from {$appointment->appointedDate} to {$appointment->releasedDate}",
                    ];
                })->pluck('formattedAppointment', 'id')->toArray();


                $positions = UserServiceAppointmentPosition::join('positions', 'user_service_appointment_positions.positionId', '=', 'positions.id')
                    ->whereIn('user_service_appointment_positions.userServiceAppointmentId', array_merge(
                        $currentAppointmentIds,
                        $previousAppointmentIds,
                        $currentAttachAppointmentIds,
                        $previousAttachAppointmentIds
                    ))
                    ->select(
                        'user_service_appointment_positions.*',
                        'positions.name AS position'
                    )
                    ->get();

                // Partition positions into categories based on appointment IDs
                $positionsPartitioned = $positions->groupBy(function ($position) use (
                    $currentAppointmentIds,
                    $previousAppointmentIds,
                    $currentAttachAppointmentIds,
                    $previousAttachAppointmentIds,
                ) {
                    if (in_array($position->userServiceAppointmentId, $currentAppointmentIds)) {
                        return 'currentPositions';
                    } elseif (in_array($position->userServiceAppointmentId, $previousAppointmentIds)) {
                        return 'previousPositions';
                    } elseif (in_array($position->userServiceAppointmentId, $currentAttachAppointmentIds)) {
                        return 'currentAttachPositions';
                    } elseif (in_array($position->userServiceAppointmentId, $previousAttachAppointmentIds)) {
                        return 'previousAttachPositions';
                    }
                    return null; // Ignore other cases
                });

                // Map the partitions to structured data
                $currentPositions = $positionsPartitioned->get('currentPositions', collect())
                    ->map(function ($position) {
                        return [
                            'id' => $position->id,
                            'positionName' => $position->position,
                            'details' => $position->toArray(),
                        ];
                })->values();

                $previousPositions = $positionsPartitioned->get('previousPositions', collect())
                    ->map(function ($position) {
                        return [
                            'id' => $position->id,
                            'positionName' => $position->position,
                            'details' => $position->toArray(),
                        ];
                })->values();

                $currentAttachPositions = $positionsPartitioned->get('currentAttachPositions', collect())
                    ->map(function ($position) {
                        return [
                            'id' => $position->id,
                            'positionName' => $position->position,
                            'details' => $position->toArray(),
                        ];
                })->values();

                $previousAttachPositions = $positionsPartitioned->get('previousAttachPositions', collect())
                    ->map(function ($position) {
                        return [
                            'id' => $position->id,
                            'positionName' => $position->position,
                            'details' => $position->toArray(),
                        ];
                })->values();

                $cryptedId = Crypt::encryptString(Auth::user()->id);


                //dd($family);
                return view('profile/myappointment', compact(
                    'cryptedId',
                    'currentServiceArray',
                    'previousServicesArray',
                    'currentServiceRanksArray',
                    'previousServiceRanksArray',
                    'currentAppointments',
                    'previousAppointments',
                    'currentAttachAppointments',
                    'previousAttachAppointments',
                    'currentPositions',
                    'previousPositions',
                    'currentAttachPositions',
                    'previousAttachPositions',
                    'option'
                ));


            }catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // Redirect to the search page or show an error message for invalid ID
                return redirect()->route('dashboard')->with('error', 'Invalid ID provided.');
            }

        }else{
            return redirect()->route('dashboard');
        }
    }
}
