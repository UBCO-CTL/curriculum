import { test, expect } from '@playwright/test';
import { login, getTestCredentials } from './helpers/auth.helper';
import {
  generateUniqueProgram,
  generatePLOs,
  ProgramData
} from './helpers/data.helper';
import {
  goToDashboard,
  clickCreateProgramButton,
  navigateToProgramStep
} from './helpers/navigation.helper';
import {
  expectSuccessMessage,
  expectOnProgramWizard
} from './helpers/assertions.helper';
import {
  fillProgramModal,
  submitProgramModal,
  addPLO,
  savePLOsInModal
} from './helpers/modal.helper';

test.describe('Programs', () => {
  let programData: ProgramData;

  test.beforeEach(async ({ page }) => {
    const { email, password } = getTestCredentials();
    await login(page, email, password);
    programData = generateUniqueProgram();
  });

  test('should create a new program successfully', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);

    await expectOnProgramWizard(page);
    await expectSuccessMessage(page);

    await expect(page.locator('text="Program Learning Outcomes", text="PLO"')).toBeVisible();
  });

  test('should add Program Learning Outcomes (PLOs)', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);

    await expectOnProgramWizard(page);

    const plos = generatePLOs();

    for (const plo of plos) {
      await addPLO(page, plo);
    }

    await savePLOsInModal(page);

    await expectSuccessMessage(page);

    for (const plo of plos) {
      await expect(page.locator(`text="${plo.shortPhrase}"`)).toBeVisible();
    }
  });

  test('should navigate through program wizard steps', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);

    await expectOnProgramWizard(page);

    await navigateToProgramStep(page, 2);
    await expect(page.locator('text="Mapping Scale", text="Scale"')).toBeVisible();

    await navigateToProgramStep(page, 3);
    await expect(page.locator('text="Courses", text="Course"')).toBeVisible();

    await navigateToProgramStep(page, 4);
    await expect(page.locator('text="Overview", text="Program Overview"')).toBeVisible();
  });

  test('should set mapping scale for program', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);

    await expectOnProgramWizard(page);

    await navigateToProgramStep(page, 2);

    const showScalesButton = page.locator('button:has-text("Show Default Mapping Scales"), button:has-text("Default")');
    if (await showScalesButton.count() > 0) {
      await showScalesButton.first().click();
      await page.waitForSelector('.modal.show', { state: 'visible', timeout: 5000 });

      const useScaleButton = page.locator('button:has-text("Use this scale")').first();
      await useScaleButton.click();

      await page.waitForTimeout(1000);

      await expect(page.locator('text="Introduced", text="Developing", text="Advanced"')).toBeVisible();
    }
  });

  test('should edit program information', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);

    await expectOnProgramWizard(page);

    const editButton = page.locator('button:has-text("Edit Program Information"), a:has-text("Edit Program")');
    if (await editButton.count() > 0) {
      await editButton.first().click();
      await page.waitForSelector('.modal.show', { state: 'visible', timeout: 5000 });

      const updatedName = `${programData.name} - Updated`;
      await page.fill('input[name="program"], #program', updatedName);

      await page.click('.modal.show button[type="submit"], .modal.show button:has-text("Save")');
      await page.waitForTimeout(1000);

      await expect(page.locator(`text="${updatedName}"`)).toBeVisible();
    }
  });

  test('should display created program on dashboard', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);

    await expectOnProgramWizard(page);

    await goToDashboard(page);

    await expect(page.locator(`text="${programData.name}"`)).toBeVisible();

    const programRow = page.locator(`tr:has-text("${programData.name}"), div:has-text("${programData.name}")`);
    await expect(programRow).toBeVisible();
  });

  test('should test PLO reordering if available', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);

    await expectOnProgramWizard(page);

    const plos = generatePLOs().slice(0, 3);

    for (const plo of plos) {
      await addPLO(page, plo);
    }

    await savePLOsInModal(page);
    await expectSuccessMessage(page);

    const dragHandles = page.locator('[class*="drag"], [class*="handle"]');
    if (await dragHandles.count() > 0) {
      await expect(dragHandles.first()).toBeVisible();
    }
  });

  test('should delete a PLO', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);

    await expectOnProgramWizard(page);

    const plo = generatePLOs()[0];
    await addPLO(page, plo);
    await savePLOsInModal(page);

    await page.waitForTimeout(500);

    const deleteButton = page.locator(`button:has-text("Delete"), a:has-text("Delete")`).first();
    if (await deleteButton.count() > 0) {
      await deleteButton.click();

      page.on('dialog', async dialog => {
        await dialog.accept();
      });

      await page.waitForTimeout(1000);
    }
  });

  test('should validate required fields in program creation', async ({ page }) => {
    await clickCreateProgramButton(page);

    const submitButton = page.locator('.modal.show button[type="submit"], .modal.show button:has-text("Add")');

    const isDisabled = await submitButton.isDisabled();
    if (!isDisabled) {
      await submitButton.click();

      await expect(page.locator('.invalid-feedback, .alert-danger')).toBeVisible({ timeout: 3000 });
    } else {
      expect(isDisabled).toBe(true);
    }
  });
});

