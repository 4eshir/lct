<?php

class MathHelper
{
    /* количество значимых голосов. на данный момент:
     * 1,2,3,4 и 7,8,9,10 - значимые голоса (которые влияют на изменение веса)
     * 5 и 6 - нейтральные голоса, не влияют на вес
     */
    const COUNT_RELEVANT_VOTES = 4;

    // расчет веса одного пункта положительного голоса
    public static function calcPosVoteWeight($mainWeight, $maxVotes)
    {
        return (1 - $mainWeight) / $maxVotes / self::COUNT_RELEVANT_VOTES;
    }

    // расчет веса одного пункта отрицательного голоса
    public static function calcNegVoteWeight($mainWeight, $maxVotes)
    {
        return $mainWeight / $maxVotes / self::COUNT_RELEVANT_VOTES;
    }
}