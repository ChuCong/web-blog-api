<?php

namespace Database\Seeders;

use App\Core\AppConst;
use App\Models\Admin;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // add default super admin
        $rolesModel = [];
        $roles = [
            ['name' => AppConst::SUPER_ADMIN_ROLE_NAME, 'guard_name' => 'admin']
        ];
        foreach ($roles as $role) {
            $rolesModel[] = Role::updateOrCreate($role, $role);
        }

        // add default admin unit
        // $rolesModelUnit = [];
        // $rolesUnit = [
        //     ['name' => AppConst::ADMIN_ROLE_NAME, 'guard_name' => 'admin']
        // ];
        // foreach ($rolesUnit as $role) {
        //     $rolesModelUnit[] = Role::updateOrCreate($role, $role);
        // }

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Permission::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        // add const role for supper admin and admin unit
        $roleOnlySuper = ['Quản lý đơn vị'];
        $roleOnlyAdmin = ['Quản lý sub admin', 'Quản lý vai trò'];
        $roleListandViewForSubAdmin = ['view_user', 'list_user', 'view_company', 'list_company',];

        $permissionsGroups = [
            'Trang chủ' => [
                [
                    'group' => 'dashboard',
                    'name' => 'view_dashboard',
                    'title' => 'Xem trang chủ',
                ]
            ],
            'Quản lý vai trò' => [
                [
                    'group' => 'role',
                    'name' => 'create_role',
                    'title' => 'Tạo vai trò',
                ],
                [
                    'group' => 'role',
                    'name' => 'update_role',
                    'title' => 'Chỉnh sửa vai trò',
                ],
                [
                    'group' => 'role',
                    'name' => 'delete_role',
                    'title' => 'Xóa vai trò',
                ],
                [
                    'group' => 'role',
                    'name' => 'view_role',
                    'title' => 'Xem vai trò',
                ],
                [
                    'group' => 'role',
                    'name' => 'list_role',
                    'title' => 'Danh sách vai trò',
                ],
            ],
            'Quản lý admin' => [
                [
                    'group' => 'user',
                    'name' => 'create_user',
                    'title' => 'Tạo admin',
                ],
                [
                    'group' => 'user',
                    'name' => 'update_user',
                    'title' => 'Chỉnh sửa admin',
                ],
                [
                    'group' => 'user',
                    'name' => 'delete_user',
                    'title' => 'Xóa admin',
                ],
                [
                    'group' => 'user',
                    'name' => 'view_user',
                    'title' => 'Xem admin',
                ],
                [
                    'group' => 'user',
                    'name' => 'list_user',
                    'title' => 'Danh sách admin',
                ],
            ],
            'Quản lý chương trình học' => [
                [
                    'group' => 'category',
                    'name' => 'create_category',
                    'title' => 'Tạo chương trình học',
                ],
                [
                    'group' => 'category',
                    'name' => 'update_category',
                    'title' => 'Chỉnh sửa chương trình học',
                ],
                [
                    'group' => 'category',
                    'name' => 'delete_category',
                    'title' => 'Xóa chương trình học',
                ],
                [
                    'group' => 'category',
                    'name' => 'view_category',
                    'title' => 'Xem chương trình học',
                ],
                [
                    'group' => 'category',
                    'name' => 'list_category',
                    'title' => 'Danh sách chương trình học',
                ],
            ],
            'Quản lý khóa học' => [
                [
                    'group' => 'course',
                    'name' => 'create_course',
                    'title' => 'Tạo khóa học',
                ],
                [
                    'group' => 'course',
                    'name' => 'update_course',
                    'title' => 'Chỉnh sửa khóa học',
                ],
                [
                    'group' => 'course',
                    'name' => 'delete_course',
                    'title' => 'Xóa khóa học',
                ],
                [
                    'group' => 'course',
                    'name' => 'view_course',
                    'title' => 'Xem khóa học',
                ],
                [
                    'group' => 'course',
                    'name' => 'list_course',
                    'title' => 'Danh sách khóa học',
                ],
            ],
            'Quản lý học viên' => [
                [
                    'group' => 'student',
                    'name' => 'export_list_student',
                    'title' => 'Xuất danh sách học viên',
                ],
                [
                    'group' => 'student',
                    'name' => 'view_student',
                    'title' => 'Xem học viên',
                ],
                [
                    'group' => 'student',
                    'name' => 'list_student',
                    'title' => 'Danh sách học viên',
                ],
            ],
            'Quản lý tag' => [
                [
                    'group' => 'tag',
                    'name' => 'create_tag',
                    'title' => 'Tạo tag',
                ],
                [
                    'group' => 'tag',
                    'name' => 'update_tag',
                    'title' => 'Chỉnh sửa tag',
                ],
                [
                    'group' => 'tag',
                    'name' => 'delete_tag',
                    'title' => 'Xóa tag',
                ],
                [
                    'group' => 'tag',
                    'name' => 'view_tag',
                    'title' => 'Xem tag',
                ],
                [
                    'group' => 'tag',
                    'name' => 'list_tag',
                    'title' => 'Danh sách tag',
                ],
            ],
            'Quản lý tài nguyên' => [
                [
                    'group' => 'resources',
                    'name' => 'create_resources',
                    'title' => 'Tạo tài nguyên',
                ],
                [
                    'group' => 'resources',
                    'name' => 'update_resources',
                    'title' => 'Chỉnh sửa tài nguyên',
                ],
                [
                    'group' => 'resources',
                    'name' => 'delete_resources',
                    'title' => 'Xóa tài nguyên',
                ],
                [
                    'group' => 'resources',
                    'name' => 'view_resources',
                    'title' => 'Xem tài nguyên',
                ],
                [
                    'group' => 'resources',
                    'name' => 'list_resources',
                    'title' => 'Danh sách tài nguyên',
                ],
            ],


            'Quản lý bình luận' => [
                [
                    'group' => 'commnet',
                    'name' => 'delete_commnet',
                    'title' => 'Xóa bình luận',
                ],
                [
                    'group' => 'commnet',
                    'name' => 'view_commnet',
                    'title' => 'Xem bình luận',
                ],
                [
                    'group' => 'commnet',
                    'name' => 'list_commnet',
                    'title' => 'Danh sách bình luận',
                ],
            ],
            'Quản lý FAQ' => [
                [
                    'group' => 'faq',
                    'name' => 'create_faq',
                    'title' => 'Tạo FAQ',
                ],
                [
                    'group' => 'faq',
                    'name' => 'update_faq',
                    'title' => 'Chỉnh sửa FAQ',
                ],
                [
                    'group' => 'faq',
                    'name' => 'delete_faq',
                    'title' => 'Xóa FAQ',
                ],
                [
                    'group' => 'faq',
                    'name' => 'view_faq',
                    'title' => 'Xem FAQ',
                ],
                [
                    'group' => 'faq',
                    'name' => 'list_faq',
                    'title' => 'Danh sách FAQ',
                ],
            ],

            'Quản lý tin tức' => [
                [
                    'group' => 'new',
                    'name' => 'create_new',
                    'title' => 'Tạo tin tức',
                ],
                [
                    'group' => 'new',
                    'name' => 'update_new',
                    'title' => 'Chỉnh sửa tin tức',
                ],
                [
                    'group' => 'new',
                    'name' => 'delete_new',
                    'title' => 'Xóa tin tức',
                ],
                [
                    'group' => 'new',
                    'name' => 'view_new',
                    'title' => 'Xem tin tức',
                ],
                [
                    'group' => 'new',
                    'name' => 'list_new',
                    'title' => 'Danh sách tin tức',
                ],
            ],
            'Quản lý chứng chỉ' => [
                [
                    'group' => 'certificate_template',
                    'name' => 'create_certificate_template',
                    'title' => 'Tạo chứng chỉ',
                ],
                [
                    'group' => 'certificate_template',
                    'name' => 'update_certificate_template',
                    'title' => 'Chỉnh sửa chứng chỉ',
                ],
                [
                    'group' => 'certificate_template',
                    'name' => 'delete_certificate_template',
                    'title' => 'Xóa chứng chỉ',
                ],
                [
                    'group' => 'certificate_template',
                    'name' => 'view_certificate_template',
                    'title' => 'Xem chứng chỉ',
                ],
                [
                    'group' => 'certificate_template',
                    'name' => 'list_certificate_template',
                    'title' => 'Danh sách chứng chỉ',
                ]
            ],
            'Quản lý gán chứng chỉ cho khóa học' => [
                [
                    'group' => 'course_certificate',
                    'name' => 'create_course_certificate',
                    'title' => 'Tạo gán chứng chỉ cho khóa học',
                ],
                [
                    'group' => 'course_certificate',
                    'name' => 'update_course_certificate',
                    'title' => 'Chỉnh sửa gán chứng chỉ cho khóa học',
                ],
                [
                    'group' => 'course_certificate',
                    'name' => 'delete_course_certificate',
                    'title' => 'Xóa gán chứng chỉ cho khóa học',
                ],
                [
                    'group' => 'course_certificate',
                    'name' => 'view_course_certificate',
                    'title' => 'Xem gán chứng chỉ cho khóa học',
                ],
                [
                    'group' => 'course_certificate',
                    'name' => 'list_course_certificate',
                    'title' => 'Danh sách gán chứng chỉ cho khóa học',
                ]
            ],
            'Quản lý thông báo' => [
                [
                    'group' => 'notification',
                    'name' => 'create_notification',
                    'title' => 'Tạo thông báo',
                ],
                [
                    'group' => 'notification',
                    'name' => 'update_notification',
                    'title' => 'Chỉnh sửa thông báo',
                ],
                [
                    'group' => 'notification',
                    'name' => 'delete_notification',
                    'title' => 'Xóa thông báo',
                ],
                [
                    'group' => 'notification',
                    'name' => 'view_notification',
                    'title' => 'Xem thông báo',
                ],
                [
                    'group' => 'notification',
                    'name' => 'list_notification',
                    'title' => 'Danh sách thông báo',
                ]
            ],
            'Quản lý thông báo đã gửi' => [
                // [
                //     'group' => 'notification',
                //     'name' => 'create_notification',
                //     'title' => 'Tạo thông báo',
                // ],
                // [
                //     'group' => 'notification',
                //     'name' => 'update_notification',
                //     'title' => 'Chỉnh sửa thông báo',
                // ],
                // [
                //     'group' => 'notification',
                //     'name' => 'delete_notification',
                //     'title' => 'Xóa thông báo',
                // ],
                // [
                //     'group' => 'notification',
                //     'name' => 'view_notification',
                //     'title' => 'Xem thông báo',
                // ],
                // [
                //     'group' => 'notification',
                //     'name' => 'list_notification',
                //     'title' => 'Danh sách thông báo',
                // ],
                [
                    'group' => 'usernotification',
                    'name' => 'user_usernotification',
                    'title' => 'Danh sách thông báo đã gửi',
                ],
            ],

            'Quản lý giảng viên' => [
                [
                    'group' => 'teacher',
                    'name' => 'create_teacher',
                    'title' => 'Tạo giảng viên',
                ],
                [
                    'group' => 'teacher',
                    'name' => 'update_teacher',
                    'title' => 'Chỉnh sửa giảng viên',
                ],
                [
                    'group' => 'teacher',
                    'name' => 'delete_teacher',
                    'title' => 'Xóa giảng viên',
                ],
                [
                    'group' => 'teacher',
                    'name' => 'view_teacher',
                    'title' => 'Xem giảng viên',
                ],
                [
                    'group' => 'teacher',
                    'name' => 'list_teacher',
                    'title' => 'Danh sách giảng viên',
                ],
            ],
            'Lịch sử truy cập' => [
                // [
                //     'group' => 'resources',
                //     'name' => 'create_resources',
                //     'title' => 'Tạo tài nguyên',
                // ],
                // [
                //     'group' => 'resources',
                //     'name' => 'update_resources',
                //     'title' => 'Chỉnh sửa tài nguyên',
                // ],
                // [
                //     'group' => 'resources',
                //     'name' => 'delete_resources',
                //     'title' => 'Xóa tài nguyên',
                // ],
                // [
                //     'group' => 'resources',
                //     'name' => 'view_resources',
                //     'title' => 'Xem tài nguyên',
                // ],
                [
                    'group' => 'userlog',
                    'name' => 'list_userlog',
                    'title' => 'Danh sách lịch sử truy cập',
                ],
            ],
            'Quản lý banner' => [
                [
                    'group' => 'banner',
                    'name' => 'create_banner',
                    'title' => 'Tạo banner',
                ],
                [
                    'group' => 'banner',
                    'name' => 'update_banner',
                    'title' => 'Chỉnh sửa banner',
                ],
                [
                    'group' => 'banner',
                    'name' => 'delete_banner',
                    'title' => 'Xóa banner',
                ],
                [
                    'group' => 'banner',
                    'name' => 'view_banner',
                    'title' => 'Xem banner',
                ],
                [
                    'group' => 'banner',
                    'name' => 'list_banner',
                    'title' => 'Danh sách banner',
                ],
            ],
            'Quản lý về Msd' => [
                [
                    'group' => 'about_msd',
                    'name' => 'create_about_msd',
                    'title' => 'Tạo về Msd',
                ],
                [
                    'group' => 'about_msd',
                    'name' => 'update_about_msd',
                    'title' => 'Chỉnh sửa về Msd',
                ],
                [
                    'group' => 'about_msd',
                    'name' => 'delete_about_msd',
                    'title' => 'Xóa về Msd',
                ],
                [
                    'group' => 'about_msd',
                    'name' => 'view_about_msd',
                    'title' => 'Xem về Msd',
                ],
                [
                    'group' => 'about_msd',
                    'name' => 'list_about_msd',
                    'title' => 'Danh sách về Msd',
                ],
            ],
            'Quản lý banner khóa học' => [
                [
                    'group' => 'course_banner',
                    'name' => 'create_course_banner',
                    'title' => 'Tạo course_banner',
                ],
                [
                    'group' => 'course_banner',
                    'name' => 'update_course_banner',
                    'title' => 'Chỉnh sửa course_banner',
                ],
                [
                    'group' => 'course_banner',
                    'name' => 'delete_course_banner',
                    'title' => 'Xóa course_banner',
                ],
                [
                    'group' => 'course_banner',
                    'name' => 'view_course_banner',
                    'title' => 'Xem course_banner',
                ],
                [
                    'group' => 'course_banner',
                    'name' => 'list_course_banner',
                    'title' => 'Danh sách course_banner',
                ],
            ],
            'Quản lý Testimonial' => [
                [
                    'group' => 'testimonial',
                    'name' => 'create_testimonial',
                    'title' => 'Tạo Testimonial',
                ],
                [
                    'group' => 'testimonial',
                    'name' => 'update_testimonial',
                    'title' => 'Chỉnh sửa Testimonial',
                ],
                [
                    'group' => 'testimonial',
                    'name' => 'delete_testimonial',
                    'title' => 'Xóa Testimonial',
                ],
                [
                    'group' => 'testimonial',
                    'name' => 'view_testimonial',
                    'title' => 'Xem Testimonial',
                ],
                [
                    'group' => 'testimonial',
                    'name' => 'list_testimonial',
                    'title' => 'Danh sách Testimonial',
                ],
            ],
            'Quản lý thống kê học tập' => [
                [
                    'group' => 'learning_statistics',
                    'name' => 'create_learning_statistics',
                    'title' => 'Tạo thống kê học tập',
                ],
                [
                    'group' => 'learning_statistics',
                    'name' => 'update_learning_statistics',
                    'title' => 'Chỉnh sửa thống kê học tập',
                ],
                [
                    'group' => 'learning_statistics',
                    'name' => 'delete_learning_statistics',
                    'title' => 'Xóa thống kê học tập',
                ],
                [
                    'group' => 'learning_statistics',
                    'name' => 'view_learning_statistics',
                    'title' => 'Xem thống kê học tập',
                ],
                // [
                //     'group' => 'learning_statistics',
                //     'name' => 'list_learning_statistics',
                //     'title' => 'Danh sách thống kê học tập',
                // ],
            ],
            'Quản lý lưu trữ' => [
                [
                    'group' => 'storage',
                    'name' => 'create_storage',
                    'title' => 'Tạo lưu trữ',
                ],
                [
                    'group' => 'storage',
                    'name' => 'update_storage',
                    'title' => 'Chỉnh sửa lưu trữ',
                ],
                [
                    'group' => 'storage',
                    'name' => 'delete_storage',
                    'title' => 'Xóa lưu trữ',
                ],
                [
                    'group' => 'storage',
                    'name' => 'view_storage',
                    'title' => 'Xem lưu trữ',
                ],
                // [
                //     'group' => 'storage',
                //     'name' => 'list_storage',
                //     'title' => 'Danh sách lưu trữ',
                // ],
            ],
            'Quản lý watermark' => [
                [
                    'group' => 'watermark',
                    'name' => 'create_watermark',
                    'title' => 'Tạo watermark',
                ],
                [
                    'group' => 'watermark',
                    'name' => 'update_watermark',
                    'title' => 'Chỉnh sửa watermark',
                ],
                [
                    'group' => 'watermark',
                    'name' => 'delete_watermark',
                    'title' => 'Xóa watermark',
                ],
                [
                    'group' => 'watermark',
                    'name' => 'view_watermark',
                    'title' => 'Xem watermark',
                ],
                // [
                //     'group' => 'watermark',
                //     'name' => 'list_watermark',
                //     'title' => 'Danh sách watermark',
                // ],
            ],
            'Quản lý cấu hình video' => [
                [
                    'group' => 'video',
                    'name' => 'create_video',
                    'title' => 'Tạo cấu hình video',
                ],
                [
                    'group' => 'video',
                    'name' => 'update_video',
                    'title' => 'Chỉnh sửa cấu hình video',
                ],
                [
                    'group' => 'video',
                    'name' => 'delete_video',
                    'title' => 'Xóa cấu hình video',
                ],
                [
                    'group' => 'video',
                    'name' => 'view_video',
                    'title' => 'Xem cấu hình video',
                ],
                // [
                //     'group' => 'video',
                //     'name' => 'list_video',
                //     'title' => 'Danh sách cấu hình video',
                // ],
            ],
            'Cấu hình khác' => [
                // [
                //     'group' => 'other',
                //     'name' => 'create_other',
                //     'title' => 'Tạo về Msd',
                // ],
                [
                    'group' => 'other',
                    'name' => 'update_other',
                    'title' => 'Chỉnh sửa cấu hình khác',
                ],
                // [
                //     'group' => 'other',
                //     'name' => 'delete_other',
                //     'title' => 'Xóa về Msd',
                // ],
                [
                    'group' => 'other',
                    'name' => 'view_other',
                    'title' => 'Xem cấu hình khác',
                ],
                // [
                //     'group' => 'other',
                //     'name' => 'list_other',
                //     'title' => 'Danh sách về Msd',
                // ],
            ],
            'Quản lý liên hệ' => [
                [
                    'group' => 'contact',
                    'name' => 'create_contact',
                    'title' => 'Tạo liên hệ',
                ],
                [
                    'group' => 'contact',
                    'name' => 'update_contact',
                    'title' => 'Chỉnh sửa liên hệ',
                ],
                [
                    'group' => 'contact',
                    'name' => 'delete_contact',
                    'title' => 'Xóa liên hệ',
                ],
                [
                    'group' => 'contact',
                    'name' => 'view_contact',
                    'title' => 'Xem liên hệ',
                ],
                [
                    'group' => 'contact',
                    'name' => 'list_contact',
                    'title' => 'Danh sách liên hệ',
                ],
            ]
        ];
        // add data permission 
        foreach ($permissionsGroups as $group => $permissions) {
            foreach ($permissions as $permission) {
                $type = AppConst::TYPE_PERMISSION_SUB_ADMIN;
                if (in_array($group, $roleOnlyAdmin)) {
                    $type = AppConst::TYPE_PERMISSION_ADMIN;
                }
                if (in_array($group, $roleOnlySuper)) {
                    $type = AppConst::TYPE_PERMISSION_SUPER_ADMIN;
                }

                Permission::updateOrCreate(['name' => $permission['name']], [
                    'title' => $permission['title'],
                    'name' => $permission['name'],
                    'guard_name' => 'admin',
                    'group' => $permission['group'],
                    'group_name' => $group,
                    'type' => in_array($permission['name'], $roleListandViewForSubAdmin) ? AppConst::TYPE_PERMISSION_SUB_ADMIN : $type,
                ]);
            }
        }
        // add permissions for supper admin
        $listPermissions = Permission::all();
        foreach ($rolesModel as $role) {
            $role->syncPermissions($listPermissions);
        }

        // add permissions for admin unit
        // $listPermissionsAdmin = Permission::where('type', '>', AppConst::TYPE_PERMISSION_SUPER_ADMIN)->get();
        // foreach ($rolesModelUnit as $role) {
        //     $role->syncPermissions($listPermissionsAdmin);
        // }

        // add role and permissions for supper admin
        $user = Admin::find(AppConst::ID_SUPER_ADMIN);
        if ($user) {
            $user->assignRole(AppConst::SUPER_ADMIN_ROLE_NAME);
        }
    }
}
