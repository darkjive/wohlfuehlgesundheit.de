/**
 * Modern Form Validation Utility
 * Provides real-time form validation with visual feedback
 */

export interface ValidationRule {
  required?: boolean;
  minLength?: number;
  maxLength?: number;
  pattern?: RegExp;
  min?: number;
  max?: number;
  custom?: (value: string) => boolean;
  message?: string;
}

export interface ValidationRules {
  [fieldName: string]: ValidationRule[];
}

export interface ValidationResult {
  isValid: boolean;
  errors: { [fieldName: string]: string };
}

/**
 * Predefined validation patterns
 */
export const ValidationPatterns = {
  email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
  phone: /^[\d\s+\-()/]+$/,
  germanZip: /^\d{5}$/,
  url: /^https?:\/\/.+/,
  number: /^\d+$/,
};

/**
 * Default error messages in German (Du-Form)
 */
export const DefaultMessages = {
  required: 'Bitte fülle dieses Feld aus.',
  email: 'Bitte gib eine gültige E-Mail-Adresse ein.',
  minLength: (min: number) => `Bitte gib mindestens ${min} Zeichen ein.`,
  maxLength: (max: number) => `Bitte gib maximal ${max} Zeichen ein.`,
  pattern: 'Bitte überprüfe deine Eingabe.',
  min: (min: number) => `Bitte gib einen Wert von mindestens ${min} ein.`,
  max: (max: number) => `Bitte gib einen Wert von maximal ${max} ein.`,
  phone: 'Bitte gib eine gültige Telefonnummer ein.',
  germanZip: 'Bitte gib eine gültige 5-stellige PLZ ein.',
  custom: 'Bitte überprüfe deine Eingabe.',
};

/**
 * Validate a single field
 */
export function validateField(value: string, rules: ValidationRule[]): string | null {
  for (const rule of rules) {
    // Required check
    if (rule.required && !value.trim()) {
      return rule.message || DefaultMessages.required;
    }

    // Skip other validations if value is empty and not required
    if (!value.trim() && !rule.required) {
      continue;
    }

    // Min length check
    if (rule.minLength !== undefined && value.length < rule.minLength) {
      return rule.message || DefaultMessages.minLength(rule.minLength);
    }

    // Max length check
    if (rule.maxLength !== undefined && value.length > rule.maxLength) {
      return rule.message || DefaultMessages.maxLength(rule.maxLength);
    }

    // Pattern check
    if (rule.pattern && !rule.pattern.test(value)) {
      return rule.message || DefaultMessages.pattern;
    }

    // Min value check (for numbers)
    if (rule.min !== undefined) {
      const numValue = parseFloat(value);
      if (isNaN(numValue) || numValue < rule.min) {
        return rule.message || DefaultMessages.min(rule.min);
      }
    }

    // Max value check (for numbers)
    if (rule.max !== undefined) {
      const numValue = parseFloat(value);
      if (isNaN(numValue) || numValue > rule.max) {
        return rule.message || DefaultMessages.max(rule.max);
      }
    }

    // Custom validation
    if (rule.custom && !rule.custom(value)) {
      return rule.message || DefaultMessages.custom;
    }
  }

  return null;
}

/**
 * Validate entire form
 */
export function validateForm(formData: FormData, rules: ValidationRules): ValidationResult {
  const errors: { [key: string]: string } = {};

  for (const [fieldName, fieldRules] of Object.entries(rules)) {
    const value = formData.get(fieldName)?.toString() || '';
    const error = validateField(value, fieldRules);

    if (error) {
      errors[fieldName] = error;
    }
  }

  return {
    isValid: Object.keys(errors).length === 0,
    errors,
  };
}

/**
 * Add visual feedback to form field
 */
export function updateFieldUI(field: HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement, error: string | null) {
  const container = field.closest('div');
  if (!container) return;

  // Remove existing error message
  const existingError = container.querySelector('.validation-error');
  if (existingError) {
    existingError.remove();
  }

  // Remove existing validation classes
  field.classList.remove('field-valid', 'field-invalid');

  if (error) {
    // Add error styling
    field.classList.add('field-invalid');

    // Create error message element
    const errorElement = document.createElement('p');
    errorElement.className = 'validation-error mt-2 text-sm text-red-600 dark:text-red-400';
    errorElement.textContent = error;
    container.appendChild(errorElement);
  } else if (field.value.trim()) {
    // Add valid styling only if field has value
    field.classList.add('field-valid');
  }
}

/**
 * Update checkbox field UI
 */
export function updateCheckboxUI(checkbox: HTMLInputElement, error: string | null) {
  const container = checkbox.closest('div')?.parentElement;
  if (!container) return;

  // Remove existing error message
  const existingError = container.querySelector('.validation-error');
  if (existingError) {
    existingError.remove();
  }

  if (error) {
    // Create error message element
    const errorElement = document.createElement('p');
    errorElement.className = 'validation-error mt-2 text-sm text-red-600 dark:text-red-400';
    errorElement.textContent = error;
    container.appendChild(errorElement);
  }
}

/**
 * Setup real-time validation for a form
 */
export function setupFormValidation(
  form: HTMLFormElement,
  rules: ValidationRules,
  options: {
    validateOnBlur?: boolean;
    validateOnInput?: boolean;
    showValidIndicator?: boolean;
  } = {}
) {
  const { validateOnBlur = true, validateOnInput = false, showValidIndicator = true } = options;

  // Handle regular input fields and select elements
  for (const fieldName of Object.keys(rules)) {
    const field = form.querySelector(`[name="${fieldName}"]`) as
      | HTMLInputElement
      | HTMLTextAreaElement
      | HTMLSelectElement
      | null;

    if (!field) continue;

    // Skip checkboxes (they're handled separately)
    if (field instanceof HTMLInputElement && field.type === 'checkbox') continue;

    if (validateOnBlur) {
      field.addEventListener('blur', () => {
        const error = validateField(field.value, rules[fieldName]);
        updateFieldUI(field, error);
      });
    }

    if (validateOnInput) {
      const eventType = field instanceof HTMLSelectElement ? 'change' : 'input';
      field.addEventListener(eventType, () => {
        // Only show validation after first blur for inputs, always for selects
        if (
          field instanceof HTMLSelectElement ||
          field.classList.contains('field-invalid') ||
          field.classList.contains('field-valid')
        ) {
          const error = validateField(field.value, rules[fieldName]);
          updateFieldUI(field, error);
        }
      });
    }

    // For select elements, also validate on change
    if (field instanceof HTMLSelectElement) {
      field.addEventListener('change', () => {
        const error = validateField(field.value, rules[fieldName]);
        updateFieldUI(field, error);
      });
    }

    // Clear validation on focus
    field.addEventListener('focus', () => {
      if (!showValidIndicator) {
        field.classList.remove('field-valid');
      }
    });
  }

  // Handle checkboxes
  const checkboxes = form.querySelectorAll('input[type="checkbox"]');
  checkboxes.forEach((checkbox) => {
    const fieldName = checkbox.getAttribute('name');
    if (!fieldName || !rules[fieldName]) return;

    checkbox.addEventListener('change', () => {
      const isChecked = (checkbox as HTMLInputElement).checked;
      const error = validateField(isChecked ? 'checked' : '', rules[fieldName]);
      updateCheckboxUI(checkbox as HTMLInputElement, error);
    });
  });
}

/**
 * Validate form on submit
 */
export function validateFormOnSubmit(form: HTMLFormElement, rules: ValidationRules): ValidationResult {
  const formData = new FormData(form);
  const result = validateForm(formData, rules);

  // Update UI for all fields
  for (const [fieldName, error] of Object.entries(result.errors)) {
    const field = form.querySelector(`[name="${fieldName}"]`) as
      | HTMLInputElement
      | HTMLTextAreaElement
      | HTMLSelectElement
      | null;

    if (field) {
      if (field.type === 'checkbox') {
        updateCheckboxUI(field as HTMLInputElement, error);
      } else {
        updateFieldUI(field, error);
      }
    }
  }

  // Scroll to first error
  if (!result.isValid) {
    const firstErrorField = Object.keys(result.errors)[0];
    const field = form.querySelector(`[name="${firstErrorField}"]`);
    if (field) {
      field.scrollIntoView({ behavior: 'smooth', block: 'center' });
      (field as HTMLElement).focus();
    }
  }

  return result;
}
