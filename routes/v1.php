<?php

use App\Http\Controllers\SendNotificationController;
use App\Http\Controllers\v1\AnnouncementController;
use App\Http\Controllers\v1\AttendanceController;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\BookController;
use App\Http\Controllers\v1\CalendarController;
use App\Http\Controllers\v1\ContentController;
use App\Http\Controllers\v1\ExamController;
use App\Http\Controllers\v1\FeeController;
use App\Http\Controllers\v1\FilterController;
use App\Http\Controllers\v1\HomeWorkController;
use App\Http\Controllers\v1\IdCardController;
use App\Http\Controllers\v1\InstructorController;
use App\Http\Controllers\v1\LibraryController;
use App\Http\Controllers\v1\McqController;
use App\Http\Controllers\v1\ReportCardController;
use App\Http\Controllers\v1\Student\ExamCopyController as StudentExamCopyController;
use App\Http\Controllers\v1\Student\MarksController as StudentMarksController;
use App\Http\Controllers\v1\Teacher\ExamCopyController as TeacherExamCopyController;
use App\Http\Controllers\v1\Teacher\MarksController as TeacherMarksController;
use App\Http\Controllers\v1\SeatingPlanController;
use App\Http\Controllers\v1\StudentContactController;
use App\Http\Controllers\v1\SubjectController;
use App\Http\Controllers\v1\SyllabusController;
use App\Http\Controllers\v1\SwitchAccountController;
use App\Http\Controllers\v1\TeacherContactController;
use App\Http\Controllers\v1\TeacherController;
use App\Http\Controllers\v1\TimeTableController;
use App\Http\Controllers\v1\TransportController;
use App\Http\Controllers\v1\UserController;
use Illuminate\Support\Facades\Route;

//Unauthentication Route
Route::get('/unauthenticate', [AuthController::class, 'unauthenticate'])->name('unauthenticate');

//v1 version
Route::prefix('v1')->group(function () {
    // Login: per-email rate limit defined in AppServiceProvider.
    Route::middleware('throttle:login')->group(function () {
        Route::post('/user/login', [UserController::class, 'studentLogin']);
        Route::post('/teacher/login', [TeacherController::class, 'teacherLogin']);
        // Switch Account — `add` is public (login + return snapshot)
        Route::post('/switch-account/add', [SwitchAccountController::class, 'add']);
    });

    // OTP / password reset: per-email rate limit defined in AppServiceProvider.
    Route::middleware('throttle:otp')->group(function () {
        Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    Route::get('/terms-and-conditions', [AuthController::class, 'termsAndConditions']);
    Route::get('/privacy-policy', [AuthController::class, 'privacyPolicy']);
    Route::get('/terms-of-use', [AuthController::class, 'termsOfUse']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1')->group(function () {

        //Save Fcm Token
        Route::post('/save-fcm-token', [SendNotificationController::class, 'saveFcmToken']);

        //Auth Api
        Route::post('/update-password', [AuthController::class, 'updatePassword']);
        Route::get('/school-info', [AuthController::class, 'schoolInfo']);

        //Rules and Regulation
        Route::get('/rules-and-regulation', [AuthController::class, 'rulesAndRegulations']);

        //About App (auth-required)
        Route::get('/about-app', [AuthController::class, 'aboutApp']);

        // User routes here
        Route::prefix('user')->group(function () {
            Route::get('/profile', [UserController::class, 'studentProfile']);

            //Admin Api
            Route::prefix('admin')->group(function () {
                Route::post('/contact', [StudentContactController::class, 'studentAdminContact']);
                Route::get('/contact-list', [StudentContactController::class, 'studentAdminContactList']);
                Route::post('/contact-reply', [StudentContactController::class, 'studentAdminContactReply']);
            });
        });

        // Teacher routes here
        Route::prefix('teacher')->group(function () {
            Route::get('/profile', [TeacherController::class, 'teacherProfile']);
            Route::get('/subject', [SubjectController::class, 'getTeacherSubject']);

            //Admin Api
            Route::prefix('admin')->group(function () {
                Route::post('/contact', [TeacherContactController::class, 'teacherAdminContact']);
                Route::get('/contact-list', [TeacherContactController::class, 'teacherAdminContactList']);
                Route::post('/contact-reply', [TeacherContactController::class, 'teacherAdminContactReply']);
            });
        });

        //Announcement Routes All
        Route::prefix('announcement')->group(function () {
            Route::post('/', [AnnouncementController::class, 'announcementList']);
            Route::get('/{id}', [AnnouncementController::class, 'getAnnouncement']);
        });

        //Library Routes All
        Route::prefix('library')->group(function () {
            Route::post('/', [LibraryController::class, 'libraryList']);
            Route::get('/{id}', [LibraryController::class, 'getLibraryItem']);
        });

        //Subject Routes All
        Route::prefix('subject')->group(function () {
            Route::get('/', [SubjectController::class, 'getAllSubject']);
        });

        //Content Routes All
        Route::prefix('content')->group(function () {
            Route::post('/upload', [ContentController::class, 'saveChapterTopic']);
            Route::post('/get', [ContentController::class, 'getChapterTopics']);
            Route::post('/chapter/{chapter_id}', [ContentController::class, 'updateChapterName']);
            Route::delete('/chapter-delete/{chapter_id}', [ContentController::class, 'deleteChapter']);
            Route::post('/topic/{topic_id}', [ContentController::class, 'updateTopic']);
            Route::delete('/topic-delete/{topic_id}', [ContentController::class, 'deleteTopic']);
        });

        //Home Work Routes All
        Route::prefix('homework')->group(function () {
            Route::post('/upload', [HomeWorkController::class, 'uploadHomeWork']);
            Route::post('/get', [HomeWorkController::class, 'allHomeWork']);
            Route::post('/update/{homework_id}', [HomeWorkController::class, 'updateHomeWork']);
            Route::delete('/delete/{homework_id}', [HomeWorkController::class, 'destroyHomeWork']);
            Route::get('/get/{homework_id}', [HomeWorkController::class, 'showSingleHomeWork']);
            Route::post('/student', [HomeWorkController::class, 'studentHomeWork']);
        });

        //Quiz Routes All
        Route::prefix('quiz')->group(function () {
            Route::post('/upload', [McqController::class, 'uploadQuiz']);
            Route::post('/get', [McqController::class, 'viewAllQuizzes']);
            Route::post('/update/{id}', [McqController::class, 'updateQuiz']);   // teacher
            Route::delete('/delete/{id}', [McqController::class, 'deleteQuiz']); // teacher
            Route::post('/submit-answer', [McqController::class, 'submitAnswer']);
            Route::post('/get/user-answer', [McqController::class, 'getUserAnswers']);
        });

        // Attendance All Routes
        Route::prefix('attendance')->group(function () {
            Route::post('/', [AttendanceController::class, 'bulkSubmitAttendance']);
            Route::post('/get-student-for-attendance', [AttendanceController::class, 'getStudentsForAttendance']);
            Route::post('/summary', [AttendanceController::class, 'getAttendanceSummary']);
            Route::post('/teacher', [AttendanceController::class, 'teacherAttendance']);
            Route::get('/today-teacher', [AttendanceController::class, 'todaysAttendance']);
            Route::get('/my', [AttendanceController::class, 'myAttendance']); // self view — student & teacher
        });

        // Syllabus Routes All
        Route::prefix('syllabus')->group(function () {
            Route::post('/', [SyllabusController::class, 'getSyllabuses']);
            Route::post('/upload', [SyllabusController::class, 'saveSyllabus']);
            Route::post('/update/{id}', [SyllabusController::class, 'updateSyllabus']);
            Route::delete('/delete/{id}', [SyllabusController::class, 'deleteSyllabus']);
            Route::post('/download/{id}', [SyllabusController::class, 'downloadSyllabus']);
        });

        //Filter Apis
        Route::prefix('filter')->group(function () {
            Route::get('/all', [AuthController::class, 'getCompleteCurriculumSimple']);
        });

        // ─── Teacher: Marks + Exam-Copy management ───────────────────────────
        // All endpoints scoped automatically to the (class, section, subject)
        // triples the teacher teaches via the timetable.
        Route::prefix('teacher')->group(function () {

            // Dropdown source — class+section+subject combos the teacher teaches
            Route::get('/classes-subjects', [TeacherMarksController::class, 'classesSubjects']);

            // Marks (text data)
            Route::prefix('marks')->group(function () {
                Route::get('/students',   [TeacherMarksController::class, 'students']); // ?standard_id=&section_id=
                Route::get('/',           [TeacherMarksController::class, 'index']);
                Route::post('/',          [TeacherMarksController::class, 'store']);
                Route::get('/{id}',       [TeacherMarksController::class, 'show'])->whereNumber('id');
                Route::put('/{id}',       [TeacherMarksController::class, 'update'])->whereNumber('id');
                Route::delete('/{id}',    [TeacherMarksController::class, 'destroy'])->whereNumber('id');
            });

            // Exam-copy PDFs
            Route::prefix('exam-copies')->group(function () {
                Route::get('/',           [TeacherExamCopyController::class, 'index']);
                Route::post('/',          [TeacherExamCopyController::class, 'store']);    // multipart
                Route::get('/{id}',       [TeacherExamCopyController::class, 'show'])->whereNumber('id');
                Route::post('/{id}',      [TeacherExamCopyController::class, 'update'])->whereNumber('id'); // multipart, accepts _method=PUT
                Route::put('/{id}',       [TeacherExamCopyController::class, 'update'])->whereNumber('id');
                Route::delete('/{id}',    [TeacherExamCopyController::class, 'destroy'])->whereNumber('id');
            });
        });

        // ─── Student: View own marks + own exam-copy PDFs ────────────────────
        // Read-only, always scoped to the authenticated student.
        Route::prefix('student')->group(function () {

            Route::prefix('marks')->group(function () {
                Route::get('/',                    [StudentMarksController::class, 'index']);
                Route::get('/overall-performance', [StudentMarksController::class, 'overallPerformance']);
            });

            Route::prefix('exam-copies')->group(function () {
                Route::get('/',     [StudentExamCopyController::class, 'index']);
                Route::get('/{id}', [StudentExamCopyController::class, 'show'])->whereNumber('id');
            });
        });

        //TimeTable Routes All
        Route::prefix('time-table')->group(function () {
            Route::post('/', [TimeTableController::class, 'getTimeTable']);
            Route::get('/student', [TimeTableController::class, 'studentTimeTable']); // student weekly view
        });

        // Calenders Api
        Route::prefix('calendar')->group(function () {
            Route::post('/events', [CalendarController::class, 'getEvents']);
            Route::get('/events/today', [CalendarController::class, 'getTodayEvents']);
            Route::get('/events/{id}', [CalendarController::class, 'getEvent']);
        });

        // Id Card Api
        Route::prefix('id-card')->group(function () {
            Route::get('/student',    [IdCardController::class, 'getStudentIdCard']);
            Route::get('/teacher',    [IdCardController::class, 'getTeacherIdCard']);
            Route::get('/admit-card', [IdCardController::class, 'getStudentAdmitCard']);
        });

        // Books Api
        Route::prefix('books')->group(function () {
            Route::get('/',    [BookController::class, 'index']);
            Route::get('/{id}', [BookController::class, 'show']);
        });

        // Instructors Api
        Route::prefix('instructors')->group(function () {
            Route::get('/',    [InstructorController::class, 'index']);
            Route::get('/{id}', [InstructorController::class, 'show']);
        });

        // Fees Api  (student only)
        Route::prefix('fees')->group(function () {
            Route::get('/summary',   [FeeController::class, 'summary']);
            Route::get('/structure', [FeeController::class, 'structure']);
            Route::get('/payments',  [FeeController::class, 'payments']);
        });

        // Transport Api
        Route::prefix('transport')->group(function () {
            Route::get('/my-route', [TransportController::class, 'myRoute']);
            Route::get('/routes',   [TransportController::class, 'routes']);
        });

        // Exams Api
        Route::prefix('exams')->group(function () {
            Route::get('/',              [ExamController::class, 'index']);
            Route::get('/{id}',          [ExamController::class, 'show']);
            Route::get('/{id}/syllabus', [ExamController::class, 'syllabus']);
        });

        // Filters Api (cascading dropdowns for app UI)
        Route::prefix('filters')->group(function () {
            Route::get('/classes',  [FilterController::class, 'classes']);
            Route::get('/sections', [FilterController::class, 'sections']);
            Route::get('/subjects', [FilterController::class, 'subjects']);
            Route::get('/exams',    [FilterController::class, 'exams']);
        });

        // Seating Plan Api  (student only)
        Route::prefix('seating-plan')->group(function () {
            Route::get('/',    [SeatingPlanController::class, 'mySeating']);
            Route::get('/all', [SeatingPlanController::class, 'all']);
        });

        // Report Card Api  (student only)
        Route::prefix('report-card')->group(function () {
            Route::get('/',    [ReportCardController::class, 'index']);
            Route::get('/{id}', [ReportCardController::class, 'show']);
        });

        // Switch Account Api
        Route::prefix('switch-account')->group(function () {
            Route::get('/me',      [SwitchAccountController::class, 'me']);
            Route::post('/remove', [SwitchAccountController::class, 'remove']);
            Route::get('/schools', [SwitchAccountController::class, 'schools']);
            Route::post('/switch', [SwitchAccountController::class, 'switch']);
        });
    });


    Route::post('/notifications/send-to-me', [SendNotificationController::class, 'sendToMe']);
});

Route::get('/admit-card/verify/{admitCardNumber}', [IdCardController::class, 'verifyAdmitCard'])->name('admit-card.verify');
