<div class="row mt-3">
    <div class="col-9 pr-0">
        <button id="btn-update-data-submit" type="submit" class="btn btn-primary btn-sm btn-block">
            <?= Yii::t('app', 'Save')?>
        </button>
    </div>
    <div class="col-3">
        <button id="btn-update-data-cancel" type="button" class="btn btn-default btn-sm btn-block"
                data-route="<?=$cancelRoute?>">
            <?=Yii::t('app', 'Cancel')?>
        </button>
    </div>
</div>