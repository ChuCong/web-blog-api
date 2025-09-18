<?php
namespace App\Exports;

use App\Repositories\UserRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings
{
    protected $userRepository;
    public function __construct()
    {
        $this->userRepository = app(UserRepository::class);
    }

    public function collection()
    {
        return $this->userRepository->getExportUsers();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Họ tên',
            'Email',
            'Số điện thoại',
            'Địa chỉ',
            'Dân tộc',
            'TÌnh trạng khuyết tật',
            'Ngày sinh',
            'Học vấn',
            'Đơn vị công tác',
            'Các khóa học đã tham gia',
            'Số khóa học tham gia',
            'Số khóa học hoàn thành',
            'Lần học bài cuối cùng',
            'Ngày tạo tài khoản'
        ];
    }
}