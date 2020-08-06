<?php
header('X-VC-TTL: 60');

$icon_sets = [];
foreach (glob('dist/*', GLOB_ONLYDIR) as $filename) {
    $icon_sets[] = basename($filename);
}

$current_icon_set = [];
if (!empty($_GET['s']) && in_array($_GET['s'], $icon_sets)) {
    $current_icon_set['key'] = $_GET['s'];
} else {
    $current_icon_set['key'] = current($icon_sets);
}

$assets = json_decode(file_get_contents('dist/rev-manifest.json'), true);
$current_icon_set['stylesheet'] = $current_icon_set['key'] . '/woody-icons.css';
if (!empty($assets) && !empty($assets[$current_icon_set['stylesheet']])) {
    $current_icon_set['stylesheet'] = $assets[$current_icon_set['stylesheet']];
}

$current_icon_set['name'] = ucfirst(str_replace('icons_set_', '', $current_icon_set['key']));

$icons = yaml_parse_file(__DIR__ . '/dist/' . $current_icon_set['key'] . '/woody-icons.yml');
$icons = $icons['icons'];
sort($icons);
$current_icon_set['icons'] = $icons;
?>
<!doctype html>
<html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <link rel='shortcut icon' type='image/x-icon' href="favicon.ico">
    <title><?php echo $current_icon_set['name']; ?> | Woody Icons</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="/dist/<?php echo $current_icon_set['stylesheet']; ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/css/bootstrap.min.css" integrity="sha384-VCmXjywReHh4PwowAiWNagnWcLhlEJLA5buUprzK8rxFgeH0kww/aWY76TfkUoSX" crossorigin="anonymous">

    <style>
        .wicon {
            font-size: 60px;
        }

        h2 {
            font-size: 11px;
        }

        .header {
            max-width: 700px;
        }

        .card-deck .card {
            min-width: 220px;
        }
    </style>
    </head>

    <body>
    <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
        <span class="my-0 mr-md-auto font-weight-normal"></span>
        <nav class="my-2 my-md-0 mr-md-3">
            <?php foreach ($icon_sets as $icon_set): ?>
                <a class="p-2 text-dark" href="/?s=<?php echo $icon_set; ?>"><?php echo ucfirst(str_replace('icons_set_', '', $icon_set)); ?></a>
            <?php endforeach; ?>
        </nav>
    </div>

    <div class="header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
        <h1 class="display-4">Icônes <?php echo $current_icon_set['name']; ?></h1>
        <!--<p class="lead"></p>-->
    </div>

    <div class="container-fluid">
        <div class="card-deck mb-3 text-center">
            <?php foreach ($current_icon_set['icons'] as $icon): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h2 class="my-0 font-weight-normal"><?php echo $icon; ?></h2>
                </div>
                <div class="card-body">
                    <span class="wicon wicon-woody-icons <?php echo $icon; ?>"></span>
                    <a href="/icons/icons_set_01/<?php echo str_replace('wicon-', '', $icon); ?>.svg" class="btn btn-sm btn-block btn-outline-primary">Télécharger</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <footer class="pt-4 my-md-5 pt-md-5 border-top">
            <div class="row">
                <div class="col-12 col-md">
                    <!-- <img class="mb-2" src="/docs/4.5/assets/brand/bootstrap-solid.svg" alt="" width="24" height="24">-->
                    <small class="d-block mb-3 text-muted">© <?php echo date('Y'); ?></small>
                </div>
            </div>
        </footer>
    </div>

    </body>
</html>
