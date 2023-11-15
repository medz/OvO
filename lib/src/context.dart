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
  final List<OvoContext> _children = [];

  Iterable<OvoContext> get children => _children;
  OvoContext get root => parent?.root ?? this;

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

  OvoContext nest(Object? data, [String? segment]) {
    final child = OvoContext(
      data,
      parent: this,
      segment: segment,
      throwMode: throwMode,
    );

    _children.add(child);

    return child;
  }

  @override
  Iterator<OvoIssue> get iterator => _issues.iterator;

  OvoContext operator +(OvoIssue issue) => this.._issues.add(issue);

  bool get passed {
    for (final issue in children) {
      if (!issue.passed) return false;
    }

    return _issues.isEmpty;
  }

  void currentIssuesClear() => _issues.clear();
  void issuesClear() {
    for (final child in children) {
      child.issuesClear();
    }

    currentIssuesClear();
  }
}
