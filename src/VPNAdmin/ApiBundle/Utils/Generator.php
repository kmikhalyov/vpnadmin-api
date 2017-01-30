<?php

namespace VPNAdmin\ApiBundle\Utils;

use VPNAdmin\ApiBundle\Entity\Company;
use VPNAdmin\ApiBundle\Entity\User;
use VPNAdmin\ApiBundle\Entity\Transfer;
use Faker\Factory;

class Generator
{
    /**
     * Generate companies list
     * @param int $min The minimum count.
     * @param int $max The maximum count.
     * @return Company[]
     */
    public function generateCompanies($min = 5, $max = 10)
    {
        $companies = array();
        $tb = 1099511627776;
        $count = rand($min, $max);
        $generator = Factory::create();
        for ($i = 0; $i < $count; $i++) {
            $gmt = $generator->dateTimeBetween('-1 year', 'now', 'UTC');
            $company = new Company();
            $company->setName($generator->company);
            $company->setQuota($tb * $this->randomNumber(100, 1000));
            $company->setCreated($gmt);
            $company->setModified($gmt);
            $companies[] = $company;
        }
        return $companies;
    }

    /**
     * Generate users list.
     * @param Company[] $companies The Company list.
     * @param int $min The minimum count of users for each company.
     * @param type $max The maximum count of users for each company.
     * @return User[]
     */
    public function generateUsers(array $companies, $min = 5, $max = 50)
    {
        $users = array();
        $generator = Factory::create();
        foreach ($companies as $company) {
            $count = rand($min, $max);
            $dateFrom = $company->getCreated();
            for ($i = 0; $i < $count; $i++) {
                $gmt = $generator->dateTimeBetween($dateFrom, 'now', 'UTC');
                $user = new User();
                $user->setCompany($company);
                $user->setName($generator->name);
                $user->setEmail($generator->email);
                $user->setCreated($gmt);
                $user->setModified($gmt);
                $users[] = $user;
            }
        }
        return $users;
    }

    public function generateTransfers($users)
    {
        // Generate transfers for the last 6 months.
        $months = 6;
        $transfers = array();
        $generator = Factory::create();

        foreach ($users as $user) {
            // The number of user transfers.
            $number = mt_rand(50, 500);
            // Current month.
            $month = $months;
            // The number of transfers per month.
            $monthsLimit = $this->numberSplitRandom($number, $months);
            $limit = $monthsLimit[0];

            for ($i = 0; $i < $number; $i++) {
                // Build resource.
                $transfers[] = $this->generateTransfer($generator, $user, $month - 1);

                if ($i + 1 >= $limit) {
                    $limit += next($monthsLimit);
                    $month--;
                }
            }
        }
        return $transfers;
    }

    /**
     * Generate random transfer data
     * 
     * @param \Faker\Generator $generator The generator instance
     * @param User $user The user transfer instance.
     * @param int $month Prev month number
     * @return Transfer
     */
    protected function generateTransfer(\Faker\Generator $generator, User $user, $month = null)
    {
        $tb = 1099511627776;
        $maxSize = $tb * 10;
        // Generate date.
        if ($month) {
            $start = "first day of -{$month} months 00:00:00";
            $end = "last day of -{$month} months 23:59:59";
        } else {
            $start = 'first day of this month 00:00:00';
            $end = 'now';
        }
        $created = $generator->dateTimeBetween($start, $end, 'UTC');
        // Build resource.
        $transfer = new Transfer();
        $transfer->setUser($user);
        $transfer->setResource($generator->url);
        $transfer->setTransferred($this->randomNumber(100, $maxSize));
        $transfer->setCreated($created);
        return $transfer;
    }

    protected function randomNumber($min, $max)
    {
        $difference   = bcadd(bcsub($max,$min),1);
        $rand_percent = bcdiv(mt_rand(), mt_getrandmax(), 8); // 0 - 1.0
        return bcadd($min, bcmul($difference, $rand_percent, 8), 0);
    }

    /**
     * Split number into random pieces.
     * @param int $number The number to split
     * @param int $count The elements count.
     * @return array|boolean
     */
    protected function numberSplitRandom($number, $count)
    {
        if ($number < $count) {
            return false;
        }
        $arr = array_fill(0, $count, 1);
        for ($i = $count; $i < $number; $i++) {
            $index = mt_rand(0, $count - 1);
            $arr[$index]++;
        }
        return $arr;
    }
}
