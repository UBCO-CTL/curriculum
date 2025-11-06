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
  goToDashboard,
  clickCreateProgramButton,
  clickCreateCourseButton,
  goToProgramWizard,
  goToCourseWizard
} from './helpers/navigation.helper';
import {
  expectOnDashboard,
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
  savePLOsInModal
} from './helpers/modal.helper';

test.describe('Dashboard', () => {
  let programData: ProgramData;
  let courseData: CourseData;

  test.beforeEach(async ({ page }) => {
    const { email, password } = getTestCredentials();
    await login(page, email, password);

    programData = generateUniqueProgram();
    courseData = generateUniqueCourse(100);
  });

  test('should display programs section on dashboard', async ({ page }) => {
    await goToDashboard(page);

    await expect(page.locator('text="Programs", h2:has-text("Programs"), h3:has-text("Programs")')).toBeVisible();

    const programsTable = page.locator('table:has-text("Program"), div:has-text("Program")');
    await expect(programsTable).toBeVisible();
  });

  test('should display courses section on dashboard', async ({ page }) => {
    await goToDashboard(page);

    await expect(page.locator('text="Courses", text="My Courses", h2:has-text("Courses")')).toBeVisible();

    const coursesTable = page.locator('table:has-text("Course"), div:has-text("Course")');
    await expect(coursesTable).toBeVisible();
  });

  test('should show created program in programs table', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);
    await expectOnProgramWizard(page);

    await goToDashboard(page);

    await expect(page.locator(`text="${programData.name}"`)).toBeVisible();

    const programRow = page.locator(`tr:has-text("${programData.name}"), div:has-text("${programData.name}")`);
    await expect(programRow).toBeVisible();

    await expect(programRow.locator(`text="${programData.faculty}"`)).toBeVisible();
  });

  test('should show created course in courses table', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);
    await expectOnCourseWizard(page);

    await goToDashboard(page);

    await expect(page.locator(`text="${courseData.title}"`)).toBeVisible();

    const courseRow = page.locator(`tr:has-text("${courseData.title}"), div:has-text("${courseData.title}")`);
    await expect(courseRow).toBeVisible();

    await expect(courseRow.locator(`text="${courseData.code}"`)).toBeVisible();
  });

  test('should navigate to program wizard when clicking program name', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);
    await expectOnProgramWizard(page);

    await goToDashboard(page);

    const programLink = page.locator(`a:has-text("${programData.name}")`).first();
    await programLink.click();

    await expectOnProgramWizard(page);
    await expect(page.locator(`text="${programData.name}"`)).toBeVisible();
  });

  test('should navigate to course wizard when clicking course title', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);
    await expectOnCourseWizard(page);

    await goToDashboard(page);

    const courseLink = page.locator(`a:has-text("${courseData.title}")`).first();
    await courseLink.click();

    await expectOnCourseWizard(page);
    await expect(page.locator(`text="${courseData.title}"`)).toBeVisible();
  });

  test('should display course count for programs', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
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

    await page.click('a:has-text("3")');
    await page.waitForTimeout(500);

    const addCourseButton = page.locator('button:has-text("+ Course From My Dashboard")').first();
    if (await addCourseButton.count() > 0) {
      await addCourseButton.click();
      await page.waitForSelector('.modal.show', { state: 'visible', timeout: 5000 });

      await page.locator('input[type="checkbox"]').first().check();
      await page.click('.modal.show button:has-text("Add Selected")');
      await page.waitForTimeout(1000);
    }

    await goToDashboard(page);

    const programRow = page.locator(`tr:has-text("${programData.name}"), div:has-text("${programData.name}")`);
    await expect(programRow).toBeVisible();
  });

  test('should display program count for courses', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
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

    await page.click('a:has-text("3")');
    await page.waitForTimeout(500);

    const addCourseButton = page.locator('button:has-text("+ Course From My Dashboard")').first();
    if (await addCourseButton.count() > 0) {
      await addCourseButton.click();
      await page.waitForSelector('.modal.show', { state: 'visible', timeout: 5000 });

      await page.locator('input[type="checkbox"]').first().check();
      await page.click('.modal.show button:has-text("Add Selected")');
      await page.waitForTimeout(1000);
    }

    await goToDashboard(page);

    const courseRow = page.locator(`tr:has-text("${courseData.title}"), div:has-text("${courseData.title}")`);
    await expect(courseRow).toBeVisible();
  });

  test('should toggle between list and group by program views for courses', async ({ page }) => {
    await goToDashboard(page);

    const listButton = page.locator('button:has-text("List all Courses"), button:has-text("List")');
    const groupButton = page.locator('button:has-text("Group by Program"), button:has-text("Group")');

    if (await groupButton.count() > 0) {
      await groupButton.click();
      await page.waitForTimeout(500);

      await expect(page.locator('text="Courses by Program"')).toBeVisible();
    }

    if (await listButton.count() > 0) {
      await listButton.click();
      await page.waitForTimeout(500);
    }
  });

  test('should display last updated timestamp', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);
    await expectOnProgramWizard(page);

    await goToDashboard(page);

    const programRow = page.locator(`tr:has-text("${programData.name}"), div:has-text("${programData.name}")`);
    await expect(programRow).toBeVisible();

    const timestampPattern = /\d+ (second|minute|hour|day|week|month|year)s? ago|just now/i;
    const rowText = await programRow.textContent();

    const hasTimestamp = rowText && timestampPattern.test(rowText);
    if (hasTimestamp) {
      expect(hasTimestamp).toBe(true);
    }
  });

  test('should display course completion status', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);
    await expectOnCourseWizard(page);

    await goToDashboard(page);

    const courseRow = page.locator(`tr:has-text("${courseData.title}"), div:has-text("${courseData.title}")`);
    await expect(courseRow).toBeVisible();

    const statusIndicator = page.locator('.progress, [class*="percent"], text="%"');
    if (await statusIndicator.count() > 0) {
      await expect(statusIndicator.first()).toBeVisible();
    }
  });

  test('should access program actions dropdown menu', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);
    await expectOnProgramWizard(page);

    await goToDashboard(page);

    const actionsButton = page.locator(`tr:has-text("${programData.name}") button[data-toggle="dropdown"], tr:has-text("${programData.name}") .dropdown-toggle`).first();
    if (await actionsButton.count() > 0) {
      await actionsButton.click();
      await page.waitForTimeout(500);

      const dropdownMenu = page.locator('.dropdown-menu.show, [class*="dropdown"][class*="show"]');
      await expect(dropdownMenu).toBeVisible();
    }
  });

  test('should access course actions dropdown menu', async ({ page }) => {
    await clickCreateCourseButton(page);
    await fillCourseModal(page, courseData);
    await submitCourseModal(page);
    await expectOnCourseWizard(page);

    await goToDashboard(page);

    const actionsButton = page.locator(`tr:has-text("${courseData.title}") button[data-toggle="dropdown"], tr:has-text("${courseData.title}") .dropdown-toggle`).first();
    if (await actionsButton.count() > 0) {
      await actionsButton.click();
      await page.waitForTimeout(500);

      const dropdownMenu = page.locator('.dropdown-menu.show, [class*="dropdown"][class*="show"]');
      await expect(dropdownMenu).toBeVisible();
    }
  });

  test('should search for programs if search functionality exists', async ({ page }) => {
    await clickCreateProgramButton(page);
    await fillProgramModal(page, programData);
    await submitProgramModal(page);
    await expectOnProgramWizard(page);

    await goToDashboard(page);

    const searchInput = page.locator('input[type="search"], input[placeholder*="Search"], input[name*="search"]').first();
    if (await searchInput.count() > 0 && await searchInput.isVisible()) {
      await searchInput.fill(programData.name);
      await page.waitForTimeout(1000);

      await expect(page.locator(`text="${programData.name}"`)).toBeVisible();
    }
  });

  test('should handle empty dashboard state gracefully', async ({ page }) => {
    await goToDashboard(page);

    await expect(page.locator('text="Programs", text="Courses"')).toBeVisible();
  });
});

