<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function spip2markdown_affiche_gauche($flux) {
  include_spip('inc/presentation');

  if ($flux['args']['exec'] == 'article'){
    $flux['data'] .=
      debut_cadre_relief('',true,'', _T('spip2markdown:spip2markdown')) .
      recuperer_fond('prive/spip2markdown-article', array('id_article'=>$flux['args']['id_article'])) .
      fin_cadre_relief(true);
    }
  return $flux;
}
