<?php
// calcul.php — API POST simple en français
header('Content-Type: application/json; charset=utf-8');

function recup_nombre_post(string $cle): float {
    if (!isset($_POST[$cle])) return 0.0;
    $val = filter_var($_POST[$cle], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    if ($val === '' || $val === null) return 0.0;
    $f = floatval($val);
    if ($f < 0) return 0.0;
    if ($f > 20) return 20.0;
    return $f;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['error' => 'Utilisez la méthode POST avec les champs requis.']);
    exit;
}

$matieres = [
    ['id' => 'francais', 'coef' => 2],
    ['id' => 'maths', 'coef' => 2],
    ['id' => 'phys', 'coef' => 1],
    ['id' => 'anglais', 'coef' => 1],
];

$sommeCoef = 0.0;
$sommePoints = 0.0;
$details = [];

foreach ($matieres as $m) {
    $id = $m['id'];
    $t1 = recup_nombre_post($id . '_t1');
    $t2 = recup_nombre_post($id . '_t2');
    $t3 = recup_nombre_post($id . '_t3');
    $moyenne = ($t1 + $t2 + $t3) / 3.0;

    if ($id === 'anglais'){
        $ecrit = recup_nombre_post('anglais_ecrit');
        $oral = recup_nombre_post('anglais_oral');
        $bepc = ($ecrit + $oral) / 2.0;
    } else {
        $bepc = recup_nombre_post($id . '_bepc');
    }

    $combinee = $bepc > 0 ? ($moyenne + $bepc) / 2.0 : $moyenne;
    $pointsParCoef = $combinee * $m['coef'];

    $details[$id] = [
        'moyenne' => round($moyenne, 2),
        'bepc' => round($bepc, 2),
        'combinee' => round($combinee, 2),
        'coef' => $m['coef'],
        'points_coefs' => round($pointsParCoef, 2)
    ];

    $sommeCoef += $m['coef'];
    $sommePoints += $pointsParCoef;
}

$mo = $sommeCoef ? ($sommePoints / $sommeCoef) : 0.0;

$reponse = [
    'succes' => true,
    'mo' => round($mo, 2),
    'total_points' => round($sommePoints, 2),
    'details' => $details
];

echo json_encode($reponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
?>