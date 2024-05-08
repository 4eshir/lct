<?php

use app\models\work\AgesWeightChangeableWork;

class WeightChangeService
{
    const SPORT_COEF = 'sport';
    const GAME_COEF = 'game';
    const EDUCATIONAL_COEF = 'educational';
    const RECREATION_COEF = 'recreation';

    public function changeWeight($ageInterval, $targetCoef, $maxCountByAge, $vote)
    {
        $entity = AgesWeightChangeableWork::find()->where(['ages_interval_id' => $ageInterval])->one();
        if ($vote <= 4) {
            $this->negativeVote($entity, $targetCoef, $maxCountByAge, $vote);
        }

        if ($vote >= 7) {
            $this->positiveVote($entity, $targetCoef, $maxCountByAge, $vote);
        }
    }

    private function positiveVote($entity, $targetCoef, $maxCountByAge, $vote)
    {
        switch ($targetCoef) {
            case self::SPORT_COEF:
                $entity->sport_weight += MathHelper::calcPosVoteWeight();
                break;
            case self::EDUCATIONAL_COEF:
                $entity->educational_weight += MathHelper::calcPosVoteWeight();
                break;
        }
    }

    private function negativeVote($entity, $targetCoef, $maxCountByAge, $vote)
    {

    }
}