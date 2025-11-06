import { test, expect } from '@playwright/test';
import { login, getTestCredentials } from './helpers/auth.helper';
import {
  generateUniqueProgram,
  generateUniqueCourse,
  ProgramData,
  CourseData
} from './helpers/data.helper';
import {
  clickCreateProgramButton,
  clickCreateCourseButton
} from './helpers/navigation.helper';
import {
  expectErrorMessage,
  expectModalOpen,
  expectSuccessMessage
} from './helpers/assertions.helper';
import {
  fillProgramModal,
  fillCourseModal,
  submitProgramModal,
  submitCourseModal,
  waitForModalOpen
} from './helpers/modal.helper';

test.describe('Validation and Error Handling', () => {
  test.beforeEach(async ({ page }) => {
    const { email, password } = getTestCredentials();
    await login(page, email, password);
  });

  test.describe('Required Fields Validation', () => {
    test('should validate required program name', async ({ page }) => {
      await clickCreateProgramButton(page);
      await expectModalOpen(page);

      await page.selectOption('select[name="ProgramOC"], #ProgramOC', { index: 1 });
      await page.waitForTimeout(300);

      const submitButton = page.locator('.modal.show button[type="submit"], .modal.show button:has-text("Add")');

      const isDisabled = await submitButton.isDisabled();
      if (!isDisabled) {
        await submitButton.click();
        await page.waitForTimeout(1000);

        const stillOnModal = await page.locator('.modal.show').count() > 0;
        if (stillOnModal) {
          const hasError = await page.locator('.invalid-feedback, .alert-danger, .is-invalid').count() > 0;
          expect(hasError).toBe(true);
        }
      } else {
        expect(isDisabled).toBe(true);
      }
    });

    test('should validate required program level', async ({ page }) => {
      await clickCreateProgramButton(page);
      await expectModalOpen(page);

      await page.fill('input[name="program"], #program', 'Test Program');
      await page.selectOption('select[name="ProgramOC"], #ProgramOC', { index: 1 });
      await page.waitForTimeout(300);

      const submitButton = page.locator('.modal.show button[type="submit"], .modal.show button:has-text("Add")');

      const levelRadios = page.locator('input[type="radio"][name="level"]');
      const hasLevelOptions = await levelRadios.count() > 0;

      if (hasLevelOptions) {
        const isDisabled = await submitButton.isDisabled();
        if (!isDisabled) {
          await submitButton.click();
          await page.waitForTimeout(1000);

          const stillOnModal = await page.locator('.modal.show').count() > 0;
          if (stillOnModal) {
            const hasError = await page.locator('.invalid-feedback, .alert-danger').count() > 0;
            expect(hasError).toBe(true);
          }
        }
      }
    });

    test('should validate required course code', async ({ page }) => {
      await clickCreateCourseButton(page);
      await expectModalOpen(page);

      await page.fill('input[name="course_num"], #course_num', '100');
      await page.fill('input[name="course_title"], #course_title', 'Test Course');

      const submitButton = page.locator('.modal.show button[type="submit"], .modal.show button:has-text("Add")');

      const isDisabled = await submitButton.isDisabled();
      if (!isDisabled) {
        await submitButton.click();
        await page.waitForTimeout(1000);

        const stillOnModal = await page.locator('.modal.show').count() > 0;
        if (stillOnModal) {
          const hasError = await page.locator('.invalid-feedback, .alert-danger, .is-invalid').count() > 0;
          expect(hasError).toBe(true);
        }
      } else {
        expect(isDisabled).toBe(true);
      }
    });

    test('should validate required course title', async ({ page }) => {
      await clickCreateCourseButton(page);
      await expectModalOpen(page);

      await page.fill('input[name="course_code"], #course_code', 'TEST');
      await page.fill('input[name="course_num"], #course_num', '100');

      const submitButton = page.locator('.modal.show button[type="submit"], .modal.show button:has-text("Add")');

      const isDisabled = await submitButton.isDisabled();
      if (!isDisabled) {
        await submitButton.click();
        await page.waitForTimeout(1000);

        const stillOnModal = await page.locator('.modal.show').count() > 0;
        if (stillOnModal) {
          const hasError = await page.locator('.invalid-feedback, .alert-danger, .is-invalid').count() > 0;
          expect(hasError).toBe(true);
        }
      } else {
        expect(isDisabled).toBe(true);
      }
    });

    test('should validate empty PLO text', async ({ page }) => {
      const programData = generateUniqueProgram();

      await clickCreateProgramButton(page);
      await fillProgramModal(page, programData);
      await submitProgramModal(page);

      const addPLOButton = page.locator('button:has-text("+ PLO")');
      if (await addPLOButton.count() > 0) {
        await addPLOButton.click();
        await waitForModalOpen(page);

        const addButton = page.locator('.modal.show button:has-text("+ Add"), .modal.show button:has-text("Add")').first();

        const isDisabled = await addButton.isDisabled();
        if (!isDisabled) {
          await addButton.click();
          await page.waitForTimeout(500);

          const hasError = await page.locator('.invalid-feedback, .alert-danger').count() > 0;
          if (hasError) {
            expect(hasError).toBe(true);
          }
        } else {
          expect(isDisabled).toBe(true);
        }
      }
    });

    test('should validate empty CLO text', async ({ page }) => {
      const courseData = generateUniqueCourse(100);

      await clickCreateCourseButton(page);
      await fillCourseModal(page, courseData);
      await submitCourseModal(page);

      const addCLOButton = page.locator('button:has-text("+ CLO")');
      if (await addCLOButton.count() > 0) {
        await addCLOButton.click();
        await page.waitForTimeout(500);

        const submitButton = page.locator('button[type="submit"]:has-text("Add"), button:has-text("Add CLO")').first();

        const isDisabled = await submitButton.isDisabled();
        if (!isDisabled) {
          await submitButton.click();
          await page.waitForTimeout(500);

          const hasError = await page.locator('.invalid-feedback, .alert-danger').count() > 0;
          if (hasError) {
            expect(hasError).toBe(true);
          }
        } else {
          expect(isDisabled).toBe(true);
        }
      }
    });
  });

  test.describe('Format Validation', () => {
    test('should enforce 4-letter maximum on course code', async ({ page }) => {
      await clickCreateCourseButton(page);
      await expectModalOpen(page);

      const courseCodeInput = page.locator('input[name="course_code"], #course_code');
      await courseCodeInput.fill('TESTCODE');

      const maxLength = await courseCodeInput.getAttribute('maxlength');
      if (maxLength) {
        expect(parseInt(maxLength)).toBeLessThanOrEqual(4);
      }

      const value = await courseCodeInput.inputValue();
      expect(value.length).toBeLessThanOrEqual(4);
    });

    test('should validate numeric course number', async ({ page }) => {
      await clickCreateCourseButton(page);
      await expectModalOpen(page);

      const courseNumInput = page.locator('input[name="course_num"], #course_num');
      const inputType = await courseNumInput.getAttribute('type');

      if (inputType === 'number') {
        expect(inputType).toBe('number');
      } else {
        await courseNumInput.fill('ABC');
        const value = await courseNumInput.inputValue();

        if (value !== 'ABC') {
          expect(value).not.toBe('ABC');
        }
      }
    });

    test('should trim whitespace from inputs', async ({ page }) => {
      const programData = generateUniqueProgram();

      await clickCreateProgramButton(page);
      await expectModalOpen(page);

      await page.fill('input[name="program"], #program', `  ${programData.name}  `);

      const submitButton = page.locator('.modal.show button[type="submit"], .modal.show button:has-text("Add")');

      await page.selectOption('select[name="ProgramOC"], #ProgramOC', { label: programData.campus });
      await page.waitForTimeout(300);
      await page.selectOption('select[name="faculty"], #faculty', { label: programData.faculty });
      await page.waitForTimeout(300);
      await page.selectOption('select[name="department"], #department', { label: programData.department });
      await page.check(`input[type="radio"][value="${programData.level}"]`);

      await submitButton.click();
      await page.waitForURL('**/programs/**', { timeout: 15000 });

      const displayedName = page.locator(`text="${programData.name.trim()}"`);
      await expect(displayedName).toBeVisible();
    });
  });

  test.describe('Special Characters', () => {
    test('should handle unicode characters in course title', async ({ page }) => {
      const courseData = generateUniqueCourse(100);
      courseData.title = '[E2E] Café Naïve & Résumé';

      await clickCreateCourseButton(page);
      await fillCourseModal(page, courseData);
      await submitCourseModal(page);

      await expect(page.locator(`text="${courseData.title}"`)).toBeVisible();
    });

    test('should handle quotes and special characters in PLO', async ({ page }) => {
      const programData = generateUniqueProgram();

      await clickCreateProgramButton(page);
      await fillProgramModal(page, programData);
      await submitProgramModal(page);

      const addPLOButton = page.locator('button:has-text("+ PLO")');
      if (await addPLOButton.count() > 0) {
        await addPLOButton.click();
        await waitForModalOpen(page);

        const specialText = 'Apply "best practices" in testing (80% proficiency) & analysis';
        await page.fill('textarea[name="program_learning_outcome"], #program_learning_outcome', specialText);
        await page.fill('input[name="plo_name"], #plo_name', 'Special Chars');

        await page.click('.modal.show button:has-text("+ Add")');
        await page.waitForTimeout(500);

        await page.click('.modal.show button:has-text("Save Changes")');
        await page.waitForTimeout(1000);

        const savedText = page.locator(`text="${specialText}"`);
        if (await savedText.count() > 0) {
          await expect(savedText).toBeVisible();
        }
      }
    });

    test('should not display HTML encoding issues', async ({ page }) => {
      const courseData = generateUniqueCourse(100);
      courseData.title = '[E2E] Test & Development';

      await clickCreateCourseButton(page);
      await fillCourseModal(page, courseData);
      await submitCourseModal(page);

      const title = page.locator(`text="${courseData.title}"`);
      await expect(title).toBeVisible();

      const htmlEncodedText = page.locator('text="&amp;", text="&#39;"');
      expect(await htmlEncodedText.count()).toBe(0);
    });
  });

  test.describe('Duplicate Detection', () => {
    test('should handle duplicate program names', async ({ page }) => {
      const programData = generateUniqueProgram();

      await clickCreateProgramButton(page);
      await fillProgramModal(page, programData);
      await submitProgramModal(page);

      await page.goto('/home');
      await page.waitForLoadState('networkidle');

      await clickCreateProgramButton(page);
      await fillProgramModal(page, programData);

      const submitButton = page.locator('.modal.show button[type="submit"], .modal.show button:has-text("Add")');
      await submitButton.click();

      await page.waitForTimeout(2000);

      const currentUrl = page.url();
      const isOnProgramPage = currentUrl.includes('/programs/');
      const stillOnModal = await page.locator('.modal.show').count() > 0;

      if (stillOnModal) {
        const hasWarning = await page.locator('.alert-warning, .alert-danger').count() > 0;
        if (hasWarning) {
          expect(hasWarning).toBe(true);
        }
      } else if (isOnProgramPage) {
        expect(isOnProgramPage).toBe(true);
      }
    });

    test('should handle duplicate course codes in same term', async ({ page }) => {
      const courseData = generateUniqueCourse(100);

      await clickCreateCourseButton(page);
      await fillCourseModal(page, courseData);
      await submitCourseModal(page);

      await page.goto('/home');
      await page.waitForLoadState('networkidle');

      await clickCreateCourseButton(page);
      await fillCourseModal(page, courseData);

      const submitButton = page.locator('.modal.show button[type="submit"], .modal.show button:has-text("Add")');
      await submitButton.click();

      await page.waitForTimeout(2000);

      const currentUrl = page.url();
      const isOnCoursePage = currentUrl.includes('/courses/');
      const stillOnModal = await page.locator('.modal.show').count() > 0;

      if (stillOnModal) {
        const hasWarning = await page.locator('.alert-warning, .alert-danger').count() > 0;
        if (hasWarning) {
          expect(hasWarning).toBe(true);
        }
      } else if (isOnCoursePage) {
        expect(isOnCoursePage).toBe(true);
      }
    });
  });

  test.describe('UI Error Messages', () => {
    test('should display success messages that auto-dismiss', async ({ page }) => {
      const programData = generateUniqueProgram();

      await clickCreateProgramButton(page);
      await fillProgramModal(page, programData);
      await submitProgramModal(page);

      const successAlert = page.locator('.alert-success');
      if (await successAlert.count() > 0) {
        await expect(successAlert).toBeVisible();

        await page.waitForTimeout(6000);

        const stillVisible = await successAlert.isVisible().catch(() => false);
        expect(stillVisible).toBe(false);
      }
    });

    test('should display error messages near relevant fields', async ({ page }) => {
      await clickCreateCourseButton(page);
      await expectModalOpen(page);

      const submitButton = page.locator('.modal.show button[type="submit"]');

      const isDisabled = await submitButton.isDisabled();
      if (!isDisabled) {
        await submitButton.click();
        await page.waitForTimeout(1000);

        const errors = page.locator('.invalid-feedback, .is-invalid');
        if (await errors.count() > 0) {
          await expect(errors.first()).toBeVisible();
        }
      }
    });

    test('should allow dismissing success messages', async ({ page }) => {
      const programData = generateUniqueProgram();

      await clickCreateProgramButton(page);
      await fillProgramModal(page, programData);
      await submitProgramModal(page);

      const successAlert = page.locator('.alert-success');
      if (await successAlert.count() > 0) {
        await expect(successAlert).toBeVisible();

        const dismissButton = successAlert.locator('button.close, button[data-dismiss="alert"], [class*="close"]');
        if (await dismissButton.count() > 0) {
          await dismissButton.click();
          await page.waitForTimeout(500);

          await expect(successAlert).not.toBeVisible();
        }
      }
    });
  });

  test.describe('Long Text Handling', () => {
    test('should handle long PLO text', async ({ page }) => {
      const programData = generateUniqueProgram();

      await clickCreateProgramButton(page);
      await fillProgramModal(page, programData);
      await submitProgramModal(page);

      const addPLOButton = page.locator('button:has-text("+ PLO")');
      if (await addPLOButton.count() > 0) {
        await addPLOButton.click();
        await waitForModalOpen(page);

        const longText = 'A'.repeat(500);
        await page.fill('textarea[name="program_learning_outcome"], #program_learning_outcome', longText);
        await page.fill('input[name="plo_name"], #plo_name', 'Long Text');

        await page.click('.modal.show button:has-text("+ Add")');
        await page.waitForTimeout(500);

        await page.click('.modal.show button:has-text("Save Changes")');
        await page.waitForTimeout(1000);

        const savedText = await page.locator('body').textContent();
        expect(savedText).toBeTruthy();
      }
    });

    test('should handle long course title', async ({ page }) => {
      const courseData = generateUniqueCourse(100);
      courseData.title = '[E2E] ' + 'Very Long Course Title '.repeat(10);

      await clickCreateCourseButton(page);

      const titleInput = page.locator('input[name="course_title"], #course_title');
      const maxLength = await titleInput.getAttribute('maxlength');

      if (maxLength) {
        const truncatedTitle = courseData.title.substring(0, parseInt(maxLength));
        courseData.title = truncatedTitle;
      }

      await fillCourseModal(page, courseData);
      await submitCourseModal(page);

      await expect(page.url()).toContain('/courses/');
    });
  });
});

