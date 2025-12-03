import { Page } from '@playwright/test';
import { ProgramData, CourseData, PLOData, CLOData } from './data.helper';

export async function waitForModalOpen(page: Page): Promise<void> {
  await page.waitForSelector('.modal.show', { state: 'visible', timeout: 5000 });
}

export async function closeModal(page: Page): Promise<void> {
  await page.click('.modal.show button.close, .modal.show [data-dismiss="modal"]');
  await page.waitForSelector('.modal.show', { state: 'hidden', timeout: 5000 });
}

export async function fillProgramModal(page: Page, data: ProgramData): Promise<void> {
  await page.waitForSelector('#createProgramModal.show', { state: 'visible', timeout: 5000 });

  const modal = page.locator('#createProgramModal.show');

  await modal.locator('#program').fill(data.name);

  await modal.locator('#campus').selectOption({ label: data.campus });
  await page.waitForTimeout(1000);

  await modal.locator('#faculty').selectOption({ label: data.faculty });
  await page.waitForTimeout(1000);

  await modal.locator('#department').selectOption({ label: data.department });

  await modal.locator(`input[type="radio"][value="${data.level}"]`).check();
}

export async function submitProgramModal(page: Page): Promise<void> {
  await page.locator('#createProgramModal.show button[type="submit"]').click();
  await page.waitForLoadState('networkidle', { timeout: 15000 });
}

export async function fillCourseModal(page: Page, data: CourseData): Promise<void> {
  await page.waitForSelector('#createCourseModal.show', { state: 'visible', timeout: 5000 });

  await page.fill('input[name="course_code"], #course_code', data.code);
  await page.fill('input[name="course_num"], #course_num', data.number);
  await page.fill('input[name="course_title"], #course_title', data.title);

  await page.selectOption('select[name="semester"], #semester', { label: data.term });
  await page.fill('input[name="year"], #year', data.year);

  if (data.section) {
    await page.fill('input[name="section"], #section', data.section);
  }

  await page.selectOption('select[name="delivery_modality"], #delivery_modality', { label: data.deliveryMode });

  const standardSelect = page.locator('select[name="standard_category_id"], select:has(option:text("Bachelor\'s degree level standards"))');
  if (await standardSelect.count() > 0) {
    await standardSelect.selectOption({ label: data.standardsLevel });
  }
}

export async function submitCourseModal(page: Page): Promise<void> {
  await page.click('.modal.show button[type="submit"], .modal.show button:has-text("Add")');
  await page.waitForURL('**/courses/**', { timeout: 15000 });
}

export async function addPLO(page: Page, plo: PLOData): Promise<void> {
  await page.click('button:has-text("+ PLO")');
  await waitForModalOpen(page);

  await page.fill('textarea[name="program_learning_outcome"], #program_learning_outcome', plo.fullText);
  await page.fill('input[name="plo_name"], #plo_name', plo.shortPhrase);

  if (plo.category) {
    await page.selectOption('select[name="category"]', { label: plo.category });
  }

  await page.click('.modal.show button:has-text("+ Add"), .modal.show button:has-text("Add")');
  await page.waitForTimeout(500);
}

export async function savePLOsInModal(page: Page): Promise<void> {
  await page.click('.modal.show button:has-text("Save Changes"), .modal.show button:has-text("Save")');
  await page.waitForSelector('.modal.show', { state: 'hidden', timeout: 5000 });
}

export async function addCLO(page: Page, clo: CLOData): Promise<void> {
  await page.click('button:has-text("+ CLO")');
  await page.waitForSelector('textarea[name="learning_outcome"], #learning_outcome', { state: 'visible', timeout: 5000 });

  await page.fill('textarea[name="learning_outcome"], #learning_outcome', clo.text);

  await page.click('button[type="submit"]:has-text("Add"), button:has-text("Add CLO")');
  await page.waitForTimeout(500);
}

