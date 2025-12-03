export interface ProgramData {
  name: string;
  campus: string;
  faculty: string;
  department: string;
  level: string;
}

export interface CourseData {
  code: string;
  number: string;
  title: string;
  term: string;
  year: string;
  section?: string;
  deliveryMode: string;
  standardsLevel: string;
}

export interface PLOData {
  fullText: string;
  shortPhrase: string;
  category?: string;
}

export interface CLOData {
  text: string;
}

export function getTimestamp(): string {
  return Date.now().toString();
}

export function generateUniqueProgram(): ProgramData {
  const timestamp = getTimestamp();
  return {
    name: `[E2E-${timestamp}] Bachelor of Testing`,
    campus: 'Okanagan',
    faculty: 'Irving K. Barber Faculty of Science',
    department: 'Biology',
    level: 'Bachelors',
  };
}

export function generateUniqueCourse(suffix: number = 100): CourseData {
  const timestamp = getTimestamp();
  const shortTimestamp = timestamp.substring(timestamp.length - 4);
  return {
    code: `E${shortTimestamp}`,
    number: suffix.toString(),
    title: `[E2E-${timestamp}] Test Course ${suffix}`,
    term: 'Winter Term 2',
    year: '2025',
    section: '',
    deliveryMode: 'Online',
    standardsLevel: "Bachelor's degree level standards",
  };
}

export function generatePLOs(): PLOData[] {
  return [
    {
      fullText: 'Demonstrate comprehensive understanding of sleep hygiene principles and their application in daily life',
      shortPhrase: 'Sleep Hygiene',
    },
    {
      fullText: 'Apply knowledge of respiratory wellness and its impact on sleep quality',
      shortPhrase: 'Respiratory Wellness',
    },
    {
      fullText: 'Analyze circadian rhythms and their role in optimal sleep patterns',
      shortPhrase: 'Circadian Science',
    },
    {
      fullText: 'Evaluate dream patterns and interpret their psychological significance',
      shortPhrase: 'Dream Analysis',
    },
    {
      fullText: 'Apply ethical principles in the context of napping and rest practices',
      shortPhrase: 'Ethics of Napping',
    },
  ];
}

export function generateCLOs(courseNumber: number): CLOData[] {
  const baseCLOs = [
    { text: 'Identify the physiological mechanisms and processes' },
    { text: 'Demonstrate proper techniques in various contexts' },
    { text: 'Analyze the social and psychological implications' },
    { text: 'Apply theoretical knowledge to practical scenarios' },
  ];

  return baseCLOs.slice(0, 3);
}

