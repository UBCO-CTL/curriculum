import { Page } from '@playwright/test';

export async function login(page: Page, email: string, password: string): Promise<void> {
  await page.goto('/login');
  await page.fill('#email', email);
  await page.fill('#password', password);
  await page.click('button[type="submit"]');
  await page.waitForURL('**/home', { timeout: 30000 });
}

export async function logout(page: Page): Promise<void> {
  await page.goto('/admin/logout');
  await page.waitForLoadState('networkidle');
}

export function getTestCredentials() {
  return {
    email: process.env.TEST_USER_EMAIL || 'rbuti@student.ubc.ca',
    password: process.env.TEST_USER_PASSWORD || 'password',
  };
}

