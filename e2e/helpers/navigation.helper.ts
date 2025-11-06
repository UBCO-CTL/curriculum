import { Page } from '@playwright/test';

export async function goToDashboard(page: Page): Promise<void> {
  await page.goto('/home');
  await page.waitForLoadState('networkidle');
}

export async function goToProgramWizard(page: Page, programName: string): Promise<void> {
  await goToDashboard(page);
  await page.click(`text="${programName}"`);
  await page.waitForURL('**/programs/**');
}

export async function goToCourseWizard(page: Page, courseTitle: string): Promise<void> {
  await goToDashboard(page);
  await page.click(`text="${courseTitle}"`);
  await page.waitForURL('**/courses/**');
}

export async function navigateToProgramStep(page: Page, stepNumber: number): Promise<void> {
  await page.click(`a:has-text("${stepNumber}")`);
  await page.waitForLoadState('networkidle');
}

export async function navigateToCourseStep(page: Page, stepNumber: number): Promise<void> {
  await page.click(`a:has-text("${stepNumber}")`);
  await page.waitForLoadState('networkidle');
}

export async function clickCreateProgramButton(page: Page): Promise<void> {
  await goToDashboard(page);
  await page.waitForSelector('h2:has-text("My Dashboard")');
  const createButton = page.locator('button[data-target="#createProgramModal"]');
  await createButton.click();
  await page.waitForSelector('#createProgramModal.show', { state: 'visible', timeout: 5000 });
}

export async function clickCreateCourseButton(page: Page): Promise<void> {
  await goToDashboard(page);
  await page.waitForSelector('h2:has-text("My Dashboard")');
  const createButton = page.locator('button[data-target="#createCourseModal"]');
  await createButton.click();
  await page.waitForSelector('#createCourseModal.show', { state: 'visible', timeout: 5000 });
}

