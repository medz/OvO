class OvoIssue {
  final String message;

  const OvoIssue(this.message);
}

enum OvoThrowMode {
  /// Throws the first issue.
  first,

  /// Throws all issues.
  all,
}

class OvoContext with Iterable<OvoIssue> {
  final OvoContext? parent;
  final String? segment;
  final Object? data;
  final OvoThrowMode throwMode;

  final List<OvoIssue> _issues = [];

  OvoContext(
    this.data, {
    this.parent,
    this.segment,
    required this.throwMode,
  });

  Iterable<String> get path sync* {
    if (parent != null) yield* parent!.path;
    if (segment != null) yield segment!;
  }

  OvoContext nest(Object? data, String segment) {
    return OvoContext(
      data,
      parent: this,
      segment: segment,
      throwMode: throwMode,
    );
  }

  @override
  Iterator<OvoIssue> get iterator => _issues.iterator;

  operator +(OvoIssue issue) => _issues.add(issue);
}
