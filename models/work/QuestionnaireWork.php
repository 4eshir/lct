<?php

namespace app\models\work;

use app\models\common\Questionnaire;

class QuestionnaireWork extends Questionnaire
{
    public $territories = [];

    public function fill(
        $userId,
        $agesIntervalId,
        $sportTendency,
        $recreationTendency,
        $gameTendency,
        $educationTendency,
        $territories,
        $arrangementMatrix,
        $territoryId
    )
    {
        $this->user_id = $userId;
        $this->ages_interval_id = $agesIntervalId;
        $this->sport_tendency = $sportTendency;
        $this->recreation_tendency = $recreationTendency;
        $this->game_tendency = $gameTendency;
        $this->education_tendency = $educationTendency;
        $this->territories = $territories;
        $this->arrangement_matrix = $arrangementMatrix;
        $this->territory_id = $territoryId;
    }
}