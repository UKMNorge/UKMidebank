<?php

UKMwp_innhold::registerFunctions();
UKMide::addViewData('page', getPage( UKMide::SLUG ));
UKMide::addViewData('subpages', UKMide::getSubpages());