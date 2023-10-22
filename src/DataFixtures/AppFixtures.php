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

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $question = new Question();
        $question
            //->setSurveyUuid($survey)
            ->setQuestionName('PYTANIE')
            ->setQuestionType('PRODUKT')
            ->setArrangement(1);

        $survey = new Survey();
        $survey
            ->setStatus(1)
            ->setTariffId(1)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setStartedAt(new \DateTimeImmutable())
            ->setExpiredAt(new \DateTimeImmutable())
            ->addQuestion($question);



        $answer1QuestionOne = new Answer();
        $answer1QuestionOne
            ->setAnswerName('TAK')
            ->setAnswerActionType('NO_ACTION')
            ->setPosition(1)
            ->setQuestion($question);

        $answer2QuestionOne = new Answer();
        $answer2QuestionOne
            ->setAnswerName('NIE')
            ->setAnswerActionType('NO_ACTION')
            ->setPosition(2)
            ->setQuestion($question);

        $answer1QuestionTwo = new Answer();
        $answer1QuestionTwo
            ->setAnswerName('TAK')
            ->setAnswerActionType('NO_ACTION')
            ->setPosition(1)
            ->setQuestion($question);

        $answer2QuestionTwo= new Answer();
        $answer2QuestionTwo
            ->setAnswerName('NIE')
            ->setAnswerActionType('NO_ACTION')
            ->setPosition(2)
            ->setQuestion($question);

        $question->addAnswer($answer1QuestionOne)->addAnswer($answer2QuestionOne);
        $question->addAnswer($answer2QuestionTwo)->addAnswer($answer2QuestionTwo);

        $manager->persist($survey);

        $apk = new Apk();
        $apk
            ->setCalculationId(21212121)
            ->setCreatedAt(new \DateTimeImmutable());

        $apk->addAnswer($answer1QuestionOne);
        $apk->addAnswer($answer2QuestionTwo);
        $manager->persist($apk);

        $manager->flush();
    }
}
