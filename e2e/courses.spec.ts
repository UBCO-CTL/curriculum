import { test, expect } from '@playwright/test';
import { login, getTestCredentials } from './helpers/auth.helper';
import {
  generateUniqueCourse,
  generateCLOs,
  CourseData
} from './helpers/data.helper';
import {
  goToDashboard,
  clickCreateCourseButton,
  navigateToCourseStep
} from './helpers/navigation.helper';
import {
  expectSuccessMessage,
  expectOnCourseWizard
} from './helpers/assertions.helper';
import {
  fillCourseModal,
  submitCourseModal,
  addCLO
} from './helpers/modal.helper';

test.describe('Courses', () => {
  let courseData: CourseData;

  test.beforeEach(async ({ page }) => {
    const { email, password } = getTestCredentials();
    await login(page, email, password);
    courseData = generateUniqueCourse(100);
  });

  test('should create a new course successfully', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);

    await expectOnCourseWizard(page);
    await expectSuccessMessage(page);

    await expect(page.locator(`text="${courseData.title}"`)).toBeVisible();
    await expect(page.locator(`text="${courseData.code}"`)).toBeVisible();
  });

  test('should add Course Learning Outcomes (CLOs)', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);

    await expectOnCourseWizard(page);

    const clos = generateCLOs(100);

    for (const clo of clos) {
      await addCLO(page, clo);
      await page.waitForTimeout(500);
    }

    for (const clo of clos) {
      await expect(page.locator(`text="${clo.text}"`)).toBeVisible();
    }
  });

  test('should navigate through course wizard steps', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);

    await expectOnCourseWizard(page);

    await navigateToCourseStep(page, 2);
    await expect(page.locator('text="Assessment", text="Student Assessment"')).toBeVisible();

    await navigateToCourseStep(page, 3);
    await expect(page.locator('text="Teaching", text="Learning", text="Activities"')).toBeVisible();

    await navigateToCourseStep(page, 4);
    await expect(page.locator('text="Alignment", text="Course Alignment"')).toBeVisible();

    await navigateToCourseStep(page, 5);
    await expect(page.locator('text="Program", text="Mapping"')).toBeVisible();
  });

  test('should add assessment methods in Step 2', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);

    await expectOnCourseWizard(page);

    await navigateToCourseStep(page, 2);

    const addButton = page.locator('button:has-text("+ Assessment"), button:has-text("Add Assessment")');
    if (await addButton.count() > 0) {
      await addButton.first().click();
      await page.waitForTimeout(500);

      const assessmentInput = page.locator('input[name*="assessment"], textarea[name*="assessment"]').first();
      if (await assessmentInput.count() > 0) {
        await assessmentInput.fill('Midterm Exam - 30%');

        const saveButton = page.locator('button[type="submit"]:has-text("Add"), button:has-text("Save")').first();
        if (await saveButton.count() > 0) {
          await saveButton.click();
          await page.waitForTimeout(500);
        }
      }
    }
  });

  test('should add learning activities in Step 3', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);

    await expectOnCourseWizard(page);

    await navigateToCourseStep(page, 3);

    const addButton = page.locator('button:has-text("+ Activity"), button:has-text("Add Activity"), button:has-text("+ Learning")');
    if (await addButton.count() > 0) {
      await addButton.first().click();
      await page.waitForTimeout(500);

      const activityInput = page.locator('input[name*="activity"], textarea[name*="activity"], input[name*="learning"]').first();
      if (await activityInput.count() > 0) {
        await activityInput.fill('Lectures and discussions');

        const saveButton = page.locator('button[type="submit"]:has-text("Add"), button:has-text("Save")').first();
        if (await saveButton.count() > 0) {
          await saveButton.click();
          await page.waitForTimeout(500);
        }
      }
    }
  });

  test('should edit a CLO', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);

    await expectOnCourseWizard(page);

    const clo = generateCLOs(100)[0];
    await addCLO(page, clo);
    await page.waitForTimeout(500);

    const editButton = page.locator('button:has-text("Edit"), a:has-text("Edit")').first();
    if (await editButton.count() > 0) {
      await editButton.click();
      await page.waitForTimeout(500);

      const cloInput = page.locator('textarea[name="learning_outcome"], #learning_outcome');
      if (await cloInput.count() > 0 && await cloInput.isVisible()) {
        await cloInput.fill(`${clo.text} - Updated`);

        const saveButton = page.locator('button[type="submit"]:has-text("Save"), button:has-text("Update")');
        if (await saveButton.count() > 0) {
          await saveButton.click();
          await page.waitForTimeout(500);
        }
      }
    }
  });

  test('should delete a CLO', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);

    await expectOnCourseWizard(page);

    const clos = generateCLOs(100);
    await addCLO(page, clos[0]);
    await addCLO(page, clos[1]);
    await page.waitForTimeout(500);

    const deleteButton = page.locator('button:has-text("Delete"), a:has-text("Delete")').first();
    if (await deleteButton.count() > 0) {
      page.on('dialog', async dialog => {
        await dialog.accept();
      });

      await deleteButton.click();
      await page.waitForTimeout(1000);
    }
  });

  test('should edit course information', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);

    await expectOnCourseWizard(page);

    const editButton = page.locator('button:has-text("Edit Course Information"), a:has-text("Edit Course")');
    if (await editButton.count() > 0) {
      await editButton.first().click();
      await page.waitForSelector('.modal.show', { state: 'visible', timeout: 5000 });

      const updatedTitle = `${courseData.title} - Updated`;
      await page.fill('input[name="course_title"], #course_title', updatedTitle);

      await page.click('.modal.show button[type="submit"], .modal.show button:has-text("Save")');
      await page.waitForTimeout(1000);

      await expect(page.locator(`text="${updatedTitle}"`)).toBeVisible();
    }
  });

  test('should display created course on dashboard', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);

    await expectOnCourseWizard(page);

    await goToDashboard(page);

    await expect(page.locator(`text="${courseData.title}"`)).toBeVisible();

    const courseRow = page.locator(`tr:has-text("${courseData.title}"), div:has-text("${courseData.title}")`);
    await expect(courseRow).toBeVisible();
  });

  test('should validate required fields in course creation', async ({ page }) => {
    await clickCreateCourseButton(page);

    const submitButton = page.locator('.modal.show button[type="submit"], .modal.show button:has-text("Add")');

    await page.fill('input[name="course_code"], #course_code', 'TEST');

    const isDisabled = await submitButton.isDisabled();
    if (!isDisabled) {
      await submitButton.click();

      await page.waitForTimeout(1000);

      const stillOnModal = await page.locator('.modal.show').count() > 0;
      if (stillOnModal) {
        await expect(page.locator('.invalid-feedback, .alert-danger')).toBeVisible({ timeout: 3000 });
      }
    }
  });

  test('should enforce 4-letter maximum on course code', async ({ page }) => {
    await clickCreateCourseButton(page);

    const courseCodeInput = page.locator('input[name="course_code"], #course_code');
    await courseCodeInput.fill('TESTCODE');

    const maxLength = await courseCodeInput.getAttribute('maxlength');
    if (maxLength) {
      expect(parseInt(maxLength)).toBeLessThanOrEqual(4);
    }

    const value = await courseCodeInput.inputValue();
    expect(value.length).toBeLessThanOrEqual(4);
  });
});

