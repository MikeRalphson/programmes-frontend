<?php
declare(strict_types = 1);
namespace App\Controller;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use DateTime;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Range;


class SchedulesByDayController extends BaseController
{
    public function __invoke(Pid $pid, ?string $date)
    {
        if (!is_null($date)) {
            $date = DateTime::createFromFormat('Y-m-d', $date);
            $dateErrors = DateTime::getLastErrors();
            if ($dateErrors['warning_count'] != 0 || $dateErrors['error_count'] != 0 || $date->format('Y') < 1900) {
                throw new HttpException(404, 'Invalid date');
            }
        } else {
            $date = new DateTime();
        }



        // TODO lookup Service based on pid
        // TODO get Broadcasts for this date (tv day or radio day)

        return $this->renderWithChrome('schedules/by_day.html.twig', [
            'service' => null,
        ]);
    }
}
