/**
 * Check if a variable is empty
 * @param variable
 */
export const empty = (variable: any): boolean => {
  return variable === null || variable === undefined || variable === '';
}
