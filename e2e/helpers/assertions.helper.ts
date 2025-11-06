import { Page, expect } from '@playwright/test';

export async function expectSuccessMessage(page: Page, message?: string): Promise<void> {
  const alertSelector = '.alert-success, .alert.alert-success';
  await expect(page.locator(alertSelector)).toBeVisible({ timeout: 10000 });

  if (message) {
    await expect(page.locator(alertSelector)).toContainText(message);
  }
}

export async function expectErrorMessage(page: Page, message?: string): Promise<void> {
  const alertSelector = '.alert-danger, .alert.alert-danger, .invalid-feedback';
  await expect(page.locator(alertSelector).first()).toBeVisible({ timeout: 5000 });

  if (message) {
    await expect(page.locator(alertSelector).first()).toContainText(message);
  }
}

export async function expectModalOpen(page: Page): Promise<void> {
  await expect(page.locator('.modal.show')).toBeVisible({ timeout: 5000 });
}

export async function expectModalClosed(page: Page): Promise<void> {
  await expect(page.locator('.modal.show')).toHaveCount(0, { timeout: 5000 });
}

export async function expectOnDashboard(page: Page): Promise<void> {
  await expect(page).toHaveURL(/.*\/home/, { timeout: 10000 });
}

export async function expectOnProgramWizard(page: Page): Promise<void> {
  await expect(page).toHaveURL(/.*\/programWizard\/\d+/, { timeout: 10000 });
}

export async function expectOnCourseWizard(page: Page): Promise<void> {
  await expect(page).toHaveURL(/.*\/courseWizard\/\d+/, { timeout: 10000 });
}

