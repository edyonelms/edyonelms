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
use App\Http\Controllers\v1\PerformanceController;
use App\Http\Controllers\v1\ReportCardController;
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
    Route::post('/user/login', [UserController::class, 'studentLogin']);
    Route::post('/teacher/login', [TeacherController::class, 'teacherLogin']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/terms-and-conditions', [AuthController::class, 'termsAndConditions']);
    Route::get('/privacy-policy', [AuthController::class, 'privacyPolicy']);
    Route::get('/terms-of-use', [AuthController::class, 'termsOfUse']);

    // Switch Account — `add` is public (login + return snapshot)
    Route::post('/switch-account/add', [SwitchAccountController::class, 'add']);
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

        //Performance Routes All
        Route::prefix('performance')->group(function () {
            Route::get('/exam-copies', [PerformanceController::class, 'getAllExamCopies']);
            Route::get('/exam-copies/{id}', [PerformanceController::class, 'getExamCopy']);
            Route::post('/student-performance-by-teacher', [PerformanceController::class, 'getStudentPerformanceByTeacher']);
            Route::get('/filters', [PerformanceController::class, 'getPerformanceFilters']);
            Route::get('/sections/{standardId}', [PerformanceController::class, 'getSectionsByStandard']);
            Route::post('/students', [PerformanceController::class, 'getStudentsByClass']);
            Route::delete('/exam-copies/{id}', [PerformanceController::class, 'deleteExamCopy']);
            Route::get('/download/exam-copy/{id}', [PerformanceController::class, 'downloadExamCopyPdf']);
            Route::post('/download/multiple-exam-copies', [PerformanceController::class, 'downloadMultipleExamCopiesPdf']);

            //Teacher  Upload Exam Copy
            Route::post('/upload-exam-copies', [PerformanceController::class, 'uploadExamCopy']);
            Route::post('/update-exam-copies/{id}', [PerformanceController::class, 'updateExamCopy']);
            Route::post('/bulk-upload', [PerformanceController::class, 'bulkUploadExamCopies']);
            Route::get('/teacher-subjects', [PerformanceController::class, 'getTeacherSubjects']);
            Route::get('/teacher-classes', [PerformanceController::class, 'getTeacherClasses']);
            Route::post('/check-exists', [PerformanceController::class, 'checkExamCopyExists']);

            //Student View Api
            Route::post('/student-performance', [PerformanceController::class, 'getStudentPerformance']);
        });

        //TimeTable Routes All
        Route::prefix('time-table')->group(function () {
            Route::post('/', [TimeTableController::class, 'getTimeTable']);
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
