<?php
/** assessment (C16): multi-step questionnaire → contact fields → POST /api/assessment/ */
$qs = assessment_questions(lang());
$errors = (($GLOBALS['form_old']['form_id'] ?? '') === 'assessment') ? ($GLOBALS['form_errors'] ?? []) : [];
$old = $errors ? $GLOBALS['form_old'] : [];
$total = count($qs) + 1;
$req = '<span class="req" aria-hidden="true">*</span>';
?>
<section class="section section-muted" id="assessment">
  <div class="container narrow">
    <?= section_head($s) ?>
    <form class="card assessment" method="post" action="<?= e(url('/api/assessment/')) ?>" novalidate data-assessment>
      <?php if ($errors): ?><div class="form-alert" role="alert"><?= e($errors['_form'] ?? t('form.errors.generic')) ?></div><?php endif; ?>
      <?= form_hidden_fields('assessment') ?>
      <input type="hidden" name="form_id" value="assessment">
      <div class="progress" aria-hidden="true"><span data-progress-bar></span></div>
      <p class="step-label" data-step-label aria-live="polite"></p>

      <?php foreach ($qs as $n => $q): $name = 'q_' . $q['id']; ?>
      <fieldset class="a-step" data-step="<?= $n ?>">
        <legend><span class="a-num"><?= $n + 1 ?>.</span> <?= e($q['label']) ?></legend>
        <?php if ($q['multi']): ?><p class="hint"><?= e(t('assessment.multi_hint')) ?></p><?php endif; ?>
        <div class="a-options">
          <?php foreach ($q['options'] as $v => $o):
              $checked = $q['multi'] ? in_array($v, (array) ($old[$name] ?? []), true) : (($old[$name] ?? '') === (string) $v); ?>
          <label class="a-option">
            <input type="<?= $q['multi'] ? 'checkbox' : 'radio' ?>" name="<?= $name ?><?= $q['multi'] ? '[]' : '' ?>" value="<?= e($v) ?>"<?= $checked ? ' checked' : '' ?><?= $q['multi'] ? '' : ' required' ?>>
            <span><?= e($o['label']) ?></span>
          </label>
          <?php endforeach; ?>
        </div>
        <?= field_error($errors, $name) ?>
      </fieldset>
      <?php endforeach; ?>

      <fieldset class="a-step" data-step="<?= count($qs) ?>">
        <legend><?= e(t('assessment.contact_heading')) ?></legend>
        <?php $GLOBALS['form_old'] = $old; ?>
        <div class="form-grid">
          <?php foreach ([['full_name', 'text', 'name', true], ['company', 'text', 'organization', true], ['job_title', 'text', 'organization-title', false], ['email', 'email', 'email', true], ['mobile', 'tel', 'tel', true]] as [$k, $type, $ac, $r]): ?>
          <div class="field">
            <label for="a-<?= $k ?>"><?= e(t("form.$k")) ?><?= $r ? $req : '' ?></label>
            <input id="a-<?= $k ?>" name="<?= $k ?>" type="<?= $type ?>" autocomplete="<?= $ac ?>"<?= $r ? ' required' : '' ?><?= in_array($k, ['email', 'mobile'], true) ? ' dir="ltr"' : '' ?><?= $k === 'mobile' ? ' placeholder="' . e(t('form.mobile_ph')) . '"' : '' ?> value="<?= old($k) ?>"<?= input_attrs($errors, $k) ?>>
            <?= field_error($errors, $k) ?>
          </div>
          <?php endforeach; ?>
          <div class="field field-full">
            <label class="consent"><input type="checkbox" name="consent" value="1" required<?= !empty($old['consent']) ? ' checked' : '' ?>> <span><?= rich(t('form.consent')) ?><?= $req ?></span></label>
            <?= field_error($errors, 'consent') ?>
          </div>
        </div>
      </fieldset>

      <div class="a-nav">
        <button type="button" class="btn btn-secondary" data-prev hidden><?= e(t('assessment.back')) ?></button>
        <button type="button" class="btn btn-primary" data-next hidden><?= e(t('assessment.next')) ?></button>
        <button type="submit" class="btn btn-primary" data-submit><?= e(t('assessment.submit')) ?></button>
      </div>
    </form>
  </div>
</section>
