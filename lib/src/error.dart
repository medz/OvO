import 'context.dart';

class OvoException implements Exception {
  const OvoException(this.context);

  final OvoContext context;

  Map<String, Iterable<OvoIssue>> get issues => _issuesBuilder(context);

  Map<String, Iterable<OvoIssue>> _issuesBuilder(OvoContext context) {
    final result = <String, Iterable<OvoIssue>>{
      context.path.join('.'): context,
    };

    if (context.parent != null) {
      final parent = _issuesBuilder(context.parent!);
      for (final MapEntry(key: path, value: issues) in parent.entries) {
        if (result.containsKey(path)) {
          result[path] = [...result[path]!, ...issues];
          continue;
        }

        result[path] = issues;
      }
    }

    return result;
  }
}

class OvoError extends OvoException implements Error {
  final Error error;

  const OvoError(super.context, this.error);

  @override
  StackTrace? get stackTrace => error.stackTrace;

  @override
  String toString() => error.toString();
}
