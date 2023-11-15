import 'context.dart';

class OvoException implements Exception {
  const OvoException(this.context);

  final OvoContext context;

  Map<String, Iterable<OvoIssue>> get issues => _issuesBuilder(context.root);

  Map<String, Iterable<OvoIssue>> _issuesBuilder(OvoContext context) {
    final result = <String, Iterable<OvoIssue>>{
      context.path.join('.'): context,
    };

    for (final child in context.children) {
      final childIssues = _issuesBuilder(child);
      for (final MapEntry(key: path, value: issues) in childIssues.entries) {
        if (result.containsKey(path)) {
          result[path] = [...result[path]!, ...issues];
          continue;
        }

        result[path] = issues;
      }
    }

    return result;
  }

  @override
  String toString() {
    return issues
        .map(
          (key, value) => MapEntry(key, value.map((e) => e.message)),
        )
        .toString();
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
