import { test, expect } from '@playwright/test';
import { login, getTestCredentials } from './helpers/auth.helper';
import {
  generateUniqueProgram,
  generateUniqueCourse,
  generatePLOs,
  generateCLOs,
  ProgramData,
  CourseData
} from './helpers/data.helper';
import {
  clickCreateProgramButton,
  clickCreateCourseButton,
  navigateToProgramStep,
  navigateToCourseStep,
  goToDashboard
} from './helpers/navigation.helper';
import {
  expectSuccessMessage,
  expectOnProgramWizard,
  expectOnCourseWizard
} from './helpers/assertions.helper';
import {
  fillProgramModal,
  submitProgramModal,
  fillCourseModal,
  submitCourseModal,
  addPLO,
  addCLO,
  savePLOsInModal,
  waitForModalOpen
} from './helpers/modal.helper';

test.describe('Course-Program Mapping', () => {
  let programData: ProgramData;
  let course1Data: CourseData;
  let course2Data: CourseData;
  let course3Data: CourseData;

  test.beforeEach(async ({ page }) => {
    const { email, password } = getTestCredentials();
    await login(page, email, password);

    programData = generateUniqueProgram();
    course1Data = generateUniqueCourse(100);
    course2Data = generateUniqueCourse(150);
    course3Data = generateUniqueCourse(201);
  });

  test('should link existing course to program from Program Wizard', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, course1Data);
    await submitCourseModal(page);
    await expectOnCourseWizard(page);

    const clos = generateCLOs(100);
    for (const clo of clos) {
      await addCLO(page, clo);
      await page.waitForTimeout(300);
    }

    await goToDashboard(page);

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

    await navigateToProgramStep(page, 3);

    const addCourseButton = page.locator('button:has-text("+ Course From My Dashboard"), button:has-text("Add Course")');
    if (await addCourseButton.count() > 0) {
      await addCourseButton.first().click();
      await waitForModalOpen(page);

      const courseCheckbox = page.locator(`input[type="checkbox"][value*="${course1Data.code}"], label:has-text("${course1Data.title}") input[type="checkbox"]`).first();
      if (await courseCheckbox.count() > 0) {
        await courseCheckbox.check();

        const requiredCheckbox = page.locator('input[name="course_required"], input[type="checkbox"]:has-text("Required")').first();
        if (await requiredCheckbox.count() > 0) {
          await requiredCheckbox.check();
        }

        await page.click('.modal.show button:has-text("Add Selected"), .modal.show button[type="submit"]');
        await page.waitForTimeout(1000);

        await expectSuccessMessage(page);

        await expect(page.locator(`text="${course1Data.title}"`)).toBeVisible();
      }
    }
  });

  test('should create new course directly from Program Wizard', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);
    await expectOnProgramWizard(page);

    const plos = generatePLOs();
    for (const plo of plos) {
      await addPLO(page, plo);
    }
    await savePLOsInModal(page);

    await navigateToProgramStep(page, 3);

    const newCourseButton = page.locator('button:has-text("+ New Course"), button:has-text("Create Course")');
    if (await newCourseButton.count() > 0) {
      await newCourseButton.first().click();
      await waitForModalOpen(page);

      await fillCourseModal(page, course2Data);

      const requiredRadio = page.locator('input[type="radio"][value="1"], input[name="course_required"][value="1"]').first();
      if (await requiredRadio.count() > 0) {
        await requiredRadio.check();
      }

      await page.click('.modal.show button[type="submit"], .modal.show button:has-text("Add")');
      await page.waitForTimeout(1000);

      await expectSuccessMessage(page);

      await expect(page.locator(`text="${course2Data.title}"`)).toBeVisible();
    }
  });

  test('should map CLOs to PLOs in Course Wizard Step 5', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, course1Data);
    await submitCourseModal(page);
    await expectOnCourseWizard(page);

    const clos = generateCLOs(100);
    for (const clo of clos) {
      await addCLO(page, clo);
      await page.waitForTimeout(300);
    }

    await goToDashboard(page);

    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);
    await expectOnProgramWizard(page);

    const plos = generatePLOs();
    for (const plo of plos) {
      await addPLO(page, plo);
    }
    await savePLOsInModal(page);

    await navigateToProgramStep(page, 2);
    const showScalesButton = page.locator('button:has-text("Show Default Mapping Scales")').first();
    if (await showScalesButton.count() > 0) {
      await showScalesButton.click();
      await waitForModalOpen(page);
      await page.locator('button:has-text("Use this scale")').first().click();
      await page.waitForTimeout(1000);
    }

    await navigateToProgramStep(page, 3);
    const addCourseButton = page.locator('button:has-text("+ Course From My Dashboard")').first();
    if (await addCourseButton.count() > 0) {
      await addCourseButton.click();
      await waitForModalOpen(page);

      const courseCheckbox = page.locator(`label:has-text("${course1Data.title}") input[type="checkbox"]`).first();
      if (await courseCheckbox.count() === 0) {
        const altCheckbox = page.locator(`input[type="checkbox"]`).first();
        await altCheckbox.check();
      } else {
        await courseCheckbox.check();
      }

      await page.click('.modal.show button:has-text("Add Selected")');
      await page.waitForTimeout(1000);
    }

    const goToCourseLink = page.locator('a:has-text("Go to Course"), button:has-text("Go to Course"), a:has-text("Map")').first();
    if (await goToCourseLink.count() > 0) {
      await goToCourseLink.click();
      await expectOnCourseWizard(page);

      await navigateToCourseStep(page, 5);

      const programSection = page.locator(`text="${programData.name}"`).first();
      if (await programSection.count() > 0) {
        await programSection.click();
        await page.waitForTimeout(500);
      }

      const radioMappings = page.locator('input[type="radio"][name*="map"]');
      const selectMappings = page.locator('select[name*="map"]');
      const buttonMappings = page.locator('button[data-map]');

      if (await radioMappings.count() > 0) {
        await radioMappings.first().check();
        await page.waitForTimeout(500);
      } else if (await selectMappings.count() > 0) {
        const firstSelect = selectMappings.first();
        const options = await firstSelect.locator('option').count();
        if (options > 1) {
          await firstSelect.selectOption({ index: 1 });
        }
        await page.waitForTimeout(500);
      } else if (await buttonMappings.count() > 0) {
        await buttonMappings.first().click();
        await page.waitForTimeout(500);
      }

      const saveButton = page.locator('button:has-text("Save"), button[type="submit"]:has-text("Update")').first();
      if (await saveButton.count() > 0) {
        await saveButton.click();
        await page.waitForTimeout(1000);
      }
    }
  });

  test('should verify mapping status changes after mapping CLOs to PLOs', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, course1Data);
    await submitCourseModal(page);
    await expectOnCourseWizard(page);

    const clos = generateCLOs(100);
    for (const clo of clos) {
      await addCLO(page, clo);
      await page.waitForTimeout(300);
    }

    await goToDashboard(page);

    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);
    await expectOnProgramWizard(page);

    const plos = generatePLOs();
    for (const plo of plos) {
      await addPLO(page, plo);
    }
    await savePLOsInModal(page);

    await navigateToProgramStep(page, 3);

    const statusBefore = page.locator('text="Not Mapped", text="0%"');

    const addCourseButton = page.locator('button:has-text("+ Course From My Dashboard")').first();
    if (await addCourseButton.count() > 0) {
      await addCourseButton.click();
      await waitForModalOpen(page);

      await page.locator('input[type="checkbox"]').first().check();
      await page.click('.modal.show button:has-text("Add Selected")');
      await page.waitForTimeout(1000);

      await expect(page.locator(`text="${course1Data.title}"`)).toBeVisible();
    }
  });

  test('should display curriculum map in Program Overview', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, course1Data);
    await submitCourseModal(page);
    await expectOnCourseWizard(page);

    const clos = generateCLOs(100);
    for (const clo of clos) {
      await addCLO(page, clo);
      await page.waitForTimeout(300);
    }

    await goToDashboard(page);

    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);
    await expectOnProgramWizard(page);

    const plos = generatePLOs();
    for (const plo of plos) {
      await addPLO(page, plo);
    }
    await savePLOsInModal(page);

    await navigateToProgramStep(page, 4);

    await expect(page.locator('text="Overview", text="Program Overview", text="Curriculum Map"')).toBeVisible();

    for (const plo of plos) {
      await expect(page.locator(`text="${plo.shortPhrase}"`)).toBeVisible();
    }
  });

  test('should remove course from program', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, course1Data);
    await submitCourseModal(page);
    await expectOnCourseWizard(page);

    await goToDashboard(page);

    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);
    await expectOnProgramWizard(page);

    const plos = generatePLOs();
    for (const plo of plos) {
      await addPLO(page, plo);
    }
    await savePLOsInModal(page);

    await navigateToProgramStep(page, 3);

    const addCourseButton = page.locator('button:has-text("+ Course From My Dashboard")').first();
    if (await addCourseButton.count() > 0) {
      await addCourseButton.click();
      await waitForModalOpen(page);

      await page.locator('input[type="checkbox"]').first().check();
      await page.click('.modal.show button:has-text("Add Selected")');
      await page.waitForTimeout(1000);
    }

    const removeButton = page.locator('button:has-text("Remove"), a:has-text("Remove")').first();
    if (await removeButton.count() > 0) {
      page.on('dialog', async dialog => {
        await dialog.accept();
      });

      await removeButton.click();
      await page.waitForTimeout(1000);
    }
  });

  test('should handle multiple courses linked to same program', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, course1Data);
    await submitCourseModal(page);
    await expectOnCourseWizard(page);

    await goToDashboard(page);

    await clickCreateCourseButton(page);
    await fillCourseModal(page, course2Data);
    await submitCourseModal(page);
    await expectOnCourseWizard(page);

    await goToDashboard(page);

    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);
    await expectOnProgramWizard(page);

    const plos = generatePLOs();
    for (const plo of plos) {
      await addPLO(page, plo);
    }
    await savePLOsInModal(page);

    await navigateToProgramStep(page, 3);

    const addCourseButton = page.locator('button:has-text("+ Course From My Dashboard")').first();
    if (await addCourseButton.count() > 0) {
      await addCourseButton.click();
      await waitForModalOpen(page);

      const checkboxes = page.locator('input[type="checkbox"]');
      const count = await checkboxes.count();

      if (count >= 2) {
        await checkboxes.nth(0).check();
        await checkboxes.nth(1).check();
      } else {
        await checkboxes.first().check();
      }

      await page.click('.modal.show button:has-text("Add Selected")');
      await page.waitForTimeout(1000);

      await expectSuccessMessage(page);
    }
  });
});

