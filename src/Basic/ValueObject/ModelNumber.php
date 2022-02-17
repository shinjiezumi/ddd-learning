<?php

namespace App\Basic\ValueObject;

class ModelNumber
{
    /**
     * @var string $productCode 製品コード
     */
    private string $productCode;

    /**
     * @var int $branch 枝番
     */
    private int $branch;

    /**
     * @var int $lot ロット番号
     */
    private int $lot;

    /**
     * コンストラクタ
     *
     * @param string $productCode
     * @param int $branch
     * @param int $lot
     */
    public function __construct(string $productCode, int $branch, int $lot)
    {
        if ($productCode === '') {
            throw new \InvalidArgumentException("invalid product code");
        }

        if ($branch <= 0) {
            throw new \InvalidArgumentException("invalid branch");
        }

        if ($lot <= 0) {
            throw new \InvalidArgumentException("invalid lot");
        }

        $this->productCode = $productCode;
        $this->branch = $branch;
        $this->lot = $lot;
    }

    /**
     * @return string 製品番号
     */
    public function toString(): string
    {
        return sprintf("%s-%d-%d", $this->productCode, $this->branch, $this->lot);
    }
}
