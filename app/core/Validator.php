<?php
namespace App\Core;

/**
 * Validate dữ liệu server-side đơn giản theo luật.
 * Luật: required, email, min:n, max:n, numeric, confirmed, in:a,b,c
 */
class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function validate(array $rules): bool
    {
        foreach ($rules as $field => $ruleStr) {
            $value = $this->data[$field] ?? null;
            foreach (explode('|', $ruleStr) as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $this->apply($field, $value, $name, $param);
            }
        }
        return empty($this->errors);
    }

    private function apply(string $field, $value, string $rule, ?string $param): void
    {
        $str = is_string($value) ? trim($value) : $value;
        switch ($rule) {
            case 'required':
                if ($str === null || $str === '') {
                    $this->add($field, "Trường này là bắt buộc.");
                }
                break;
            case 'email':
                if ($str !== '' && !filter_var($str, FILTER_VALIDATE_EMAIL)) {
                    $this->add($field, "Email không hợp lệ.");
                }
                break;
            case 'min':
                if (mb_strlen((string) $str) < (int) $param) {
                    $this->add($field, "Tối thiểu {$param} ký tự.");
                }
                break;
            case 'max':
                if (mb_strlen((string) $str) > (int) $param) {
                    $this->add($field, "Tối đa {$param} ký tự.");
                }
                break;
            case 'numeric':
                if ($str !== '' && !is_numeric($str)) {
                    $this->add($field, "Phải là số.");
                }
                break;
            case 'confirmed':
                if ($str !== ($this->data["{$field}_confirmation"] ?? null)) {
                    $this->add($field, "Xác nhận không khớp.");
                }
                break;
            case 'in':
                $allowed = explode(',', (string) $param);
                if (!in_array($str, $allowed, true)) {
                    $this->add($field, "Giá trị không hợp lệ.");
                }
                break;
        }
    }

    private function add(string $field, string $msg): void
    {
        $this->errors[$field][] = $msg;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }
}
