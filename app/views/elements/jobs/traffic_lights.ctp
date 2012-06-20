<?php

if (empty ($λ)) $λ = 2;

if ($λ < .001)
    $colour = 'green';
elseif ($λ < .04)
    $colour = 'yellow';
elseif ($λ < 1.0)
    $colour = 'red';
else
    $colour = 'bork';


$message = sprintf ("Your λ (lambda) value of %0.4f means that ", $λ);
switch ($colour) {
    case "green":
        $message .= "depurination due to heating is unlikely to have fragmented the DNA badly. Good results may be obtainable using smaller sample sizes.";
        break;
    case "yellow":
        $message .= "the DNA is relatively fragmented but of sufficient quality for some types of experiment.";
        break;
    case "red":
        $message .= "the DNA is badly damaged. Destructive sampling for DNA amplification should almost certainly be avoided.";
        break;
    case "bork":
        $message .= "unfortunately that your DNA is toast; fallen apart, obliterated, kaput. Any value above 1 indicates total destruction. Any extra above 1 is theoretically meaningless but the higher the number, the more utter the destruction.";
        break;
    default:
        $message .= "(we're not sure - this is an error in the program, please report it.)";
        
}

?>
<div class="<?=@$class?>">
    <div class="clearfix" style="background-image: url('/img/<?='traffic_'.$colour?>.png'); background-repeat: no-repeat; background-position: 10px 30px; padding-left: 62px; min-width: 100px; min-height: 200px;">
        <p><?=$message?></p>
        <p>Remember, this tool cannot predict that the DNA <strong>is</strong> of good quality, only that it is unlikely to have been destroyed by temperature related depurination.</p>
    </div>
</div>