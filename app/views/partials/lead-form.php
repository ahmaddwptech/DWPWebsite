<?php
/**
 * Lead form (spec B4).
 * @var string $variant default|demo|quote|short   @var string $interest pre-selected interest   @var ?string $heading   @var string $id
 */
$type = ['default' => 'lead', 'demo' => 'demo', 'quote' => 'quote', 'short' => 'short'][$variant] ?? 'lead';
$isThis = ($GLOBALS['form_old']['form_id'] ?? null) === $id;
$errors = $isThis ? ($GLOBALS['form_errors'] ?? []) : [];
if (!$isThis) { $savedOld = $GLOBALS['form_old'] ?? null; $GLOBALS['form_old'] = []; }
$req = '<span class="req" aria-hidden="true">*</span>';
$f = fn(string $k) => e(t("form.$k"));
$selInterest = $isThis ? ($GLOBALS['form_old']['interest'] ?? $interest) : $interest;
?>
<form class="lead-form" method="post" action="<?= e(url('/api/lead/')) ?>" novalidate data-lead-form data-form-type="<?= e($type) ?>" id="<?= e($id) ?>">
  <?php if ($heading): ?><h2 class="form-heading"><?= rich($heading) ?></h2><?php endif; ?>
  <?php if ($errors): ?>
    <div class="form-alert" role="alert"><?= e($errors['_form'] ?? t('form.errors.generic')) ?></div>
  <?php endif; ?>
  <?= form_hidden_fields($type) ?>
  <input type="hidden" name="form_id" value="<?= e($id) ?>">

  <div class="form-grid">
    <div class="field">
      <label for="<?= $id ?>-name"><?= $f('full_name') ?><?= $req ?></label>
      <input id="<?= $id ?>-name" name="full_name" type="text" autocomplete="name" required maxlength="120" value="<?= old('full_name') ?>"<?= input_attrs($errors, 'full_name') ?>>
      <?= field_error($errors, 'full_name') ?>
    </div>
    <div class="field">
      <label for="<?= $id ?>-company"><?= $f('company') ?><?= $req ?></label>
      <input id="<?= $id ?>-company" name="company" type="text" autocomplete="organization" required maxlength="160" value="<?= old('company') ?>"<?= input_attrs($errors, 'company') ?>>
      <?= field_error($errors, 'company') ?>
    </div>
    <?php if ($variant !== 'short'): ?>
    <div class="field">
      <label for="<?= $id ?>-job"><?= $f('job_title') ?></label>
      <input id="<?= $id ?>-job" name="job_title" type="text" autocomplete="organization-title" maxlength="120" value="<?= old('job_title') ?>">
    </div>
    <?php endif; ?>
    <div class="field">
      <label for="<?= $id ?>-email"><?= $f('email') ?><?= $req ?></label>
      <input id="<?= $id ?>-email" name="email" type="email" autocomplete="email" required maxlength="190" dir="ltr" value="<?= old('email') ?>"<?= input_attrs($errors, 'email') ?>>
      <?= field_error($errors, 'email') ?>
    </div>
    <div class="field">
      <label for="<?= $id ?>-mobile"><?= $f('mobile') ?><?= $req ?></label>
      <input id="<?= $id ?>-mobile" name="mobile" type="tel" autocomplete="tel" required maxlength="40" dir="ltr" placeholder="<?= $f('mobile_ph') ?>" value="<?= old('mobile') ?>"<?= input_attrs($errors, 'mobile') ?>>
      <?= field_error($errors, 'mobile') ?>
    </div>

    <?php if ($variant === 'short'): ?>
      <input type="hidden" name="interest" value="<?= e($interest) ?>">
    <?php else: ?>
    <div class="field">
      <label for="<?= $id ?>-employees"><?= $f('employees') ?></label>
      <select id="<?= $id ?>-employees" name="employees"><?= select_options(t('form.employee_options'), $GLOBALS['form_old']['employees'] ?? '') ?></select>
    </div>
    <div class="field">
      <label for="<?= $id ?>-interest"><?= $f('interest') ?><?= $req ?></label>
      <select id="<?= $id ?>-interest" name="interest" required<?= input_attrs($errors, 'interest') ?>><?= select_options(t('form.interest_options'), (string) $selInterest) ?></select>
      <?= field_error($errors, 'interest') ?>
    </div>
    <div class="field">
      <label for="<?= $id ?>-system"><?= $f('current_system') ?></label>
      <select id="<?= $id ?>-system" name="current_system"><?= select_options(t('form.system_options'), $GLOBALS['form_old']['current_system'] ?? '') ?></select>
    </div>
    <?php if ($variant === 'quote'): ?>
    <div class="field">
      <label for="<?= $id ?>-users"><?= $f('users_count') ?></label>
      <input id="<?= $id ?>-users" name="users_count" type="number" min="1" max="100000" inputmode="numeric" value="<?= old('users_count') ?>">
    </div>
    <?php endif; ?>
    <?php if ($variant === 'demo'): ?>
    <div class="field">
      <label for="<?= $id ?>-date"><?= $f('demo_date') ?></label>
      <input id="<?= $id ?>-date" name="demo_date" type="date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= old('demo_date') ?>">
    </div>
    <div class="field">
      <label for="<?= $id ?>-time"><?= $f('demo_time') ?></label>
      <select id="<?= $id ?>-time" name="demo_time"><?= select_options(t('form.demo_time_options'), $GLOBALS['form_old']['demo_time'] ?? '') ?></select>
    </div>
    <fieldset class="field">
      <legend><?= $f('demo_language') ?></legend>
      <div class="choice-row">
        <?php foreach (t('form.demo_language_options') as $v => $l): ?>
        <label class="choice"><input type="radio" name="demo_language" value="<?= e($v) ?>"<?= ($GLOBALS['form_old']['demo_language'] ?? lang()) === $v ? ' checked' : '' ?>> <?= e($l) ?></label>
        <?php endforeach; ?>
      </div>
    </fieldset>
    <fieldset class="field">
      <legend><?= $f('demo_format') ?></legend>
      <div class="choice-row">
        <?php foreach (t('form.demo_format_options') as $v => $l): ?>
        <label class="choice"><input type="radio" name="demo_format" value="<?= e($v) ?>"<?= ($GLOBALS['form_old']['demo_format'] ?? 'online') === $v ? ' checked' : '' ?>> <?= e($l) ?></label>
        <?php endforeach; ?>
      </div>
    </fieldset>
    <?php endif; ?>
    <div class="field field-full">
      <label for="<?= $id ?>-message"><?= $f('message') ?></label>
      <textarea id="<?= $id ?>-message" name="message" rows="4" maxlength="4000"><?= old('message') ?></textarea>
    </div>
    <?php endif; ?>

    <div class="field field-full">
      <label class="consent"><input type="checkbox" name="consent" value="1" required<?= input_attrs($errors, 'consent') ?><?= !empty($GLOBALS['form_old']['consent']) ? ' checked' : '' ?>> <span><?= rich(t('form.consent')) ?><?= $req ?></span></label>
      <?= field_error($errors, 'consent') ?>
    </div>
  </div>
  <button type="submit" class="btn btn-primary btn-block"><?= $f('send') ?></button>
</form>
<?php if (!$isThis) $GLOBALS['form_old'] = $savedOld; ?>
