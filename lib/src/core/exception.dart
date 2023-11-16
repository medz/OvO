class OvOException implements Exception {
  final Symbol code;
  final String message;
  final String? path;
  final Iterable<Exception>? exceptions;

  const OvOException(
      {required this.code, required this.message, this.path, this.exceptions});

  @override
  String toString() {
    return 'OvOException: $message at ${path ?? 'root'}';
  }
}
