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
            $validator = $this->get('validator');

            // TODO see if we can create a valid date from $date else throw 404
            $errors = $validator->validate(
                $date,
                [new Date()]
            );

            if (count($errors) > 0) throw new HttpException(404, "No found results for this date: invalid date");

            $date = DateTime::createFromFormat('Y-m-d', $date);

            // TODO make sure date is in a sensible range - year is between 1900
            // and 2100 else throw 404 (no point querying the DB for dates we know won't have any results)
            $errors = $validator->validate(
                $date->format('Y'),
                [new Range(['min' => 1900, 'max'=>2100])]
            );

            if (count($errors) > 0) throw new HttpException(404, "No found results for this date: date out of range");


        } else {
            // TODO if date is absent use today's date
            $date = new DateTime();
        }



        // TODO lookup Service based on pid
        // TODO get Broadcasts for this date (tv day or radio day)

        return $this->renderWithChrome('schedules/by_day.html.twig', [
            'service' => null,
        ]);
    }
}
