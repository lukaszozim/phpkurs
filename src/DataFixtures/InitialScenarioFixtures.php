<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Apk;
use App\Entity\Question;
use App\Entity\Response;
use App\Entity\Survey;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class InitialScenarioFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['initial_scenario'];
    }

    public function load(ObjectManager $manager): void
    {
        $questionOne = new Question();
        $questionOne
            ->setQuestionName('Pytanie 1')
            ->setQuestionType('ONE')
            ->setArrangement(1);

        $questionTwo = new Question();
        $questionTwo
            ->setQuestionName('Pytanie 2')
            ->setQuestionType('ONE')
            ->setArrangement(2);

        $survey = new Survey();
        $survey
            ->setStatus(1)
            ->setTariffId(1)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setStartedAt(new \DateTimeImmutable())
            ->setExpiredAt(new \DateTimeImmutable())
            ->addQuestion($questionOne)
            ->addQuestion($questionTwo);

        $answerYesQ1 = new Answer();
        $answerYesQ1
            ->setAnswerName('TAK')
            ->setAnswerActionType('NO_ACTION')
            ->setPosition(1)
            ->setQuestion($questionOne);

        $answerNoQ1 = new Answer();
        $answerNoQ1
            ->setAnswerName('NIE')
            ->setAnswerActionType('NO_ACTION')
            ->setPosition(2)
            ->setQuestion($questionOne);

        $answerYesQ2 = new Answer();
        $answerYesQ2
            ->setAnswerName('TAK')
            ->setAnswerActionType('NO_ACTION')
            ->setPosition(1)
            ->setQuestion($questionTwo);

        $answerNoQ2 = new Answer();
        $answerNoQ2
            ->setAnswerName('NIE')
            ->setAnswerActionType('NO_ACTION')
            ->setPosition(2)
            ->setQuestion($questionTwo);

        $questionOne
            ->addAnswer($answerYesQ1)
            ->addAnswer($answerNoQ1);

        $questionTwo
            ->addAnswer($answerYesQ2)
            ->addAnswer($answerNoQ2);

        $manager->persist($questionOne);
        $manager->persist($questionTwo);
        $manager->persist($survey);

        $apk = new Apk();
        $apk
            ->setCalculationId(1111111)
            ->setCreatedAt(new \DateTimeImmutable());

        $apk->addAnswer($answerNoQ1);
        $apk->addAnswer($answerYesQ2);
        $manager->persist($apk);

        $apk2 = new Apk();
        $apk2
            ->setCalculationId(2222222)
            ->setCreatedAt(new \DateTimeImmutable());

        $apk2->addAnswer($answerYesQ1);
        $apk2->addAnswer($answerYesQ2);
        $manager->persist($apk2);


        $manager->flush();
    }
}
