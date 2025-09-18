<?php

namespace App\Core;

class AppConst
{
    const DOMAIN_FRONTEND = 'https://topthi.vn';
    const CODE_EXCEPTION_MESSAGE = 99999;

    const UPLOAD_CONFIG_NAME = "upload_file";
    const UPLOAD_CONFIG_PATH = "/uploads/";
    const UPLOAD_TYPE_EXAM = 1;
    const UPLOAD_TYPE_CKEDITOR = 2;
    const UPLOAD_FOLDER_TYPE = [
        self::UPLOAD_TYPE_EXAM => "exams",
        self::UPLOAD_TYPE_CKEDITOR => "ckeditor",
    ];

    const MEDIA_TYPE_IMAGE = 1;
    const MEDIA_TYPE_VIDEO = 2;
    const MEDIA_TYPE_AUDIO = 3;
    const MEDIA_TYPE_PDF = 4;
    const MEDIA_TYPE_EXCEL = 5;
    const MEDIA_TYPE_ZIP = 6;
    const MEDIA_TYPE_WORD = 7;

    const FILE_UPLOAD_TYPES = [
        self::MEDIA_TYPE_IMAGE => "image",
        self::MEDIA_TYPE_VIDEO => "video",
        self::MEDIA_TYPE_AUDIO => "audio",
        self::MEDIA_TYPE_PDF => "pdf",
        self::MEDIA_TYPE_EXCEL => "excel",
        self::MEDIA_TYPE_ZIP => "zip",
        self::MEDIA_TYPE_WORD => "word"
    ];

    const FILE_UPLOAD_EXTENSION = [
        self::MEDIA_TYPE_IMAGE => ["jpg", "png", "svg", "jpeg", "JPG", "PNG", "SVG", "JPEG"],
        self::MEDIA_TYPE_VIDEO => ["mp4", "MP4"],
        self::MEDIA_TYPE_AUDIO => ["mp3", "MP3"],
        self::MEDIA_TYPE_PDF => ["pdf", "PDF"],
        self::MEDIA_TYPE_EXCEL => ["xlsx", "xls", "XLSX", "XLS"],
        self::MEDIA_TYPE_ZIP => ["zip", "ZIP"],
        self::MEDIA_TYPE_WORD => ["doc", "docx", "DOC", "DOCX"]
    ];

    const PAGE_LIMIT = NULL;

    const ID_SUPER_ADMIN = 1;
    const SUPER_ADMIN_ROLE_ID = 1;
    const SUPER_ADMIN_ROLE_NAME = "Admin Tổng";
    const ADMIN_ROLE_NAME = "Admin Đơn Vị";

    const GUARD_SANCTUM = 'admin';
    const GUARD_SANCTUM_CLIENT = 'admin_client';

    const TYPE_PASSWORD_RESET_ADMIN = 1;
    const TYPE_PASSWORD_RESET_USER = 2;

    const TYPE_ANSWER_IN_QUESTION_SINGLE_ANSWER = 1;
    const TYPE_ANSWER_IN_QUESTION_MULTIPLE_ANSWER = 2;
    const TYPE_ANSWER_IN_QUESTION_TEXT_ANSWER = 3;
    const TYPE_ANSWER_IN_QUESTION_MATCHING = 4;
    const TYPE_ANSWER_IN_QUESTION_HAS_CHILD = 5;
    const TYPE_ANSWER_IN_QUESTION_RIGHT_OR_WRONG = 6;
    const TYPE_ANSWER_IN_QUESTION = [
        self::TYPE_ANSWER_IN_QUESTION_SINGLE_ANSWER,
        self::TYPE_ANSWER_IN_QUESTION_MULTIPLE_ANSWER,
        self::TYPE_ANSWER_IN_QUESTION_TEXT_ANSWER,
        self::TYPE_ANSWER_IN_QUESTION_MATCHING,
        self::TYPE_ANSWER_IN_QUESTION_HAS_CHILD,
        self::TYPE_ANSWER_IN_QUESTION_RIGHT_OR_WRONG
    ];
    const TYPE_ANSWER_DEFAULT = 1;
    const TYPE_ANSWER_SELECT = 2;

    const TYPE_QUERY_EXAM_RANDOM = 1;
    const TYPE_QUERY_EXAM_NEW = 2;
    const TYPE_QUERY_EXAM_HOT_TAKE = 3;
    const COMPANY_IS_REQUIRE_SLUG = 1;
    const MENU_CHAR = '|-----';
    const LEVEL_QUESTION_DEFAULT = 0;
    const LEVEL_QUESTION_EASY = 1;
    const LEVEL_QUESTION_NORMAL = 2;
    const LEVEL_QUESTION_DIFFICULT = 3;
    const LEVEL_QUESTION_VERY_DIFFICULT = 4;
    const LEVEL_QUESTION = [
        self::LEVEL_QUESTION_DEFAULT => "Mặc định",
        self::LEVEL_QUESTION_EASY => "Dễ",
        self::LEVEL_QUESTION_NORMAL => "Trung bình",
        self::LEVEL_QUESTION_DIFFICULT => "Khá",
        self::LEVEL_QUESTION_VERY_DIFFICULT => "Giỏi",
    ];
    const IS_PUBLISHED = 1;
    const NO_PUBLISHED = 0;
    const IS_REVIEWED = 1;
    const NO_REVIEWED = 0;

    const TYPE_PERMISSION_SUPER_ADMIN = 1;
    const TYPE_PERMISSION_ADMIN = 2;
    const TYPE_PERMISSION_SUB_ADMIN = 3;

    const ACTIVE = 1;
    const IN_ACTIVE = 0;

    // VNPay order status
    const VNPAY_RESPONSE_SUCCESS = "00";
    const USER_TYPE_FRONT = "front";

    const NUMBER_STUDENT_IN_GROUP = 20;
    const STATUS_LIMIT_STUDENT = 'LIMIT_STUDENT';
    const MAX_RATE = 5;

    const ADMIN_TOPDETHI = 'topdethi';

    const UPLOAD_FOR_USER = 1;

    const EXAM_TYPE_LIST = 1;
    const EXAM_TYPE_PART = 2;

    const SEARCH_TYPE_EXAMS = "exams";
    const SEARCH_TYPE_QUESTIONS = "questions";

    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    const DAYS_OF_WEEK = [
        self::MONDAY => "Monday",
        self::TUESDAY => "Tuesday",
        self::WEDNESDAY => "Wednesday",
        self::THURSDAY => "Thursday",
        self::FRIDAY => "Friday",
        self::SATURDAY => "Saturday",
        self::SUNDAY => "Sunday"
    ];

    const REPEAT_TYPE_DAILY = 1;
    const REPEAT_TYPE_DAYS_OF_WEEK = 2;
    const REPEAT_TYPE_SPECIFIC_DAYS = 3;

    const REPEAT_TYPES = [
        self::REPEAT_TYPE_DAILY => "daily",
        self::REPEAT_TYPE_DAYS_OF_WEEK => "weekly",
        self::REPEAT_TYPE_SPECIFIC_DAYS => "specific_days"
    ];

    const CONTEST_NORMAL = 1;
    const CONTEST_BEFORE_START = 2;
    const CONTEST_BEFORE_START_AND_UNPAID = 3;
    const CONTEST_BEFORE_START_AND_PAID = 4;
    const CONTEST_JOIN_TIME = 5;
    const CONTEST_JOIN_TIME_AND_UNPAID = 6;
    const CONTEST_JOIN_TIME_AND_PAID = 7;
    const CONTEST_JOIN_TIME_AND_JOINED = 8;
    const CONTEST_IN_PROGRESS = 9;
    const CONTEST_IN_PROGRESS_AND_UNPAID = 10;
    const CONTEST_IN_PROGRESS_AND_PAID = 11;
    const CONTEST_IN_PROGRESS_AND_JOINED = 12;
    const CONTEST_AFTER_END = 13;
    const CONTEST_AFTER_END_AND_UNPAID = 14;
    const CONTEST_AFTER_END_AND_PAID = 15;
    const CONTEST_AFTER_END_AND_JOINED = 16;

    const TYPE_QUESTION_SCORE_SENTENCE = 1;
    const TYPE_QUESTION_SCORE_EACH_IDEA = 2;
    const TYPE_IDEA_SCORE_BY_EACH_IDEA = 1;
    const TYPE_IDEA_SCORE_BY_NUMBER_IDEA = 2;

    const CONTEST_CODE_PREFIX = "CT-";
    const EXAM_CODE_PREFIX = "EX-";

    const CONTEST_INACTIVE_STATUS = 0;
    const CONTEST_REQUEST_PUBLISH_STATUS = 1;
    const CONTEST_PUBLISH_STATUS = 2;
    const CONTEST_CLOSE_STATUS = 3;

    const CONTEST_ORDER_TYPE = 'App\Models\Group';
    const EXAM_ORDER_TYPE = 'App\Models\Exam';


    const ORDER_CREATED_STATUS = 0;
    const ORDER_CANCELED_STATUS = 1;
    const ORDER_PAID_STATUS = 2;
    const ORDER_PAID_ERROR_STATUS = 3;
    const ORDER_UNCREATED = 4;

    const ONGOING_CONTEST = 1;
    const HAS_NOT_STARTED_CONTEST = 2;
    const ENDED_CONTEST = 3;
}
