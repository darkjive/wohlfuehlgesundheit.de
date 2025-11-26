/**
 * Moderne Formular-Validierungs-Utility
 * Bietet Echtzeit-Validierung mit visuellem Feedback
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
 * Vordefinierte Validierungsmuster
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
 * Validiere ein einzelnes Feld
 */
export function validateField(value: string, rules: ValidationRule[]): string | null {
  for (const rule of rules) {
    // Pflichtfeld-Prüfung
    if (rule.required && !value.trim()) {
      return rule.message || DefaultMessages.required;
    }

    // Überspringe andere Validierungen, wenn Wert leer und nicht erforderlich
    if (!value.trim() && !rule.required) {
      continue;
    }

    // Mindestlängen-Prüfung
    if (rule.minLength !== undefined && value.length < rule.minLength) {
      return rule.message || DefaultMessages.minLength(rule.minLength);
    }

    // Maximallängen-Prüfung
    if (rule.maxLength !== undefined && value.length > rule.maxLength) {
      return rule.message || DefaultMessages.maxLength(rule.maxLength);
    }

    // Muster-Prüfung
    if (rule.pattern && !rule.pattern.test(value)) {
      return rule.message || DefaultMessages.pattern;
    }

    // Minimalwert-Prüfung (für Zahlen)
    if (rule.min !== undefined) {
      const numValue = parseFloat(value);
      if (isNaN(numValue) || numValue < rule.min) {
        return rule.message || DefaultMessages.min(rule.min);
      }
    }

    // Maximalwert-Prüfung (für Zahlen)
    if (rule.max !== undefined) {
      const numValue = parseFloat(value);
      if (isNaN(numValue) || numValue > rule.max) {
        return rule.message || DefaultMessages.max(rule.max);
      }
    }

    // Benutzerdefinierte Validierung
    if (rule.custom && !rule.custom(value)) {
      return rule.message || DefaultMessages.custom;
    }
  }

  return null;
}

/**
 * Validiere gesamtes Formular
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
 * Füge visuelles Feedback zum Formularfeld hinzu
 */
export function updateFieldUI(field: HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement, error: string | null) {
  const container = field.closest('div');
  if (!container) return;

  // Entferne existierende Fehlermeldung
  const existingError = container.querySelector('.validation-error');
  if (existingError) {
    existingError.remove();
  }

  // Entferne existierende Validierungsklassen
  field.classList.remove('field-valid', 'field-invalid');

  if (error) {
    // Füge Fehler-Styling hinzu
    field.classList.add('field-invalid');

    // Erstelle Fehlermeldungs-Element
    const errorElement = document.createElement('p');
    errorElement.className = 'validation-error mt-2 text-sm text-red-600 dark:text-red-400';
    errorElement.textContent = error;
    container.appendChild(errorElement);
  } else if (field.value.trim()) {
    // Füge Gültig-Styling nur hinzu, wenn Feld einen Wert hat
    field.classList.add('field-valid');
  }
}

/**
 * Aktualisiere Checkbox-Feld UI
 */
export function updateCheckboxUI(checkbox: HTMLInputElement, error: string | null) {
  const container = checkbox.closest('div')?.parentElement;
  if (!container) return;

  // Entferne existierende Fehlermeldung
  const existingError = container.querySelector('.validation-error');
  if (existingError) {
    existingError.remove();
  }

  if (error) {
    // Erstelle Fehlermeldungs-Element
    const errorElement = document.createElement('p');
    errorElement.className = 'validation-error mt-2 text-sm text-red-600 dark:text-red-400';
    errorElement.textContent = error;
    container.appendChild(errorElement);
  }
}

/**
 * Richte Echtzeit-Validierung für Formular ein
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

  // Behandle reguläre Eingabefelder und Select-Elemente
  for (const fieldName of Object.keys(rules)) {
    const field = form.querySelector(`[name="${fieldName}"]`) as
      | HTMLInputElement
      | HTMLTextAreaElement
      | HTMLSelectElement
      | null;

    if (!field) continue;

    // Überspringe Checkboxen (werden separat behandelt)
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
        // Zeige Validierung nur nach erstem Blur für Inputs, immer für Selects
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

    // Für Select-Elemente, validiere auch bei Änderung
    if (field instanceof HTMLSelectElement) {
      field.addEventListener('change', () => {
        const error = validateField(field.value, rules[fieldName]);
        updateFieldUI(field, error);
      });
    }

    // Lösche Validierung bei Fokus
    field.addEventListener('focus', () => {
      if (!showValidIndicator) {
        field.classList.remove('field-valid');
      }
    });
  }

  // Behandle Checkboxen
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
 * Validiere Formular beim Absenden
 */
export function validateFormOnSubmit(form: HTMLFormElement, rules: ValidationRules): ValidationResult {
  const formData = new FormData(form);
  const result = validateForm(formData, rules);

  // Aktualisiere UI für alle Felder
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

  // Scrolle zum ersten Fehler
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
