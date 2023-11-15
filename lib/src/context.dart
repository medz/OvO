class OvoIssue {
  final String kind;
  final String message;

  const OvoIssue(this.kind, this.message);
}

typedef OvoLocaleMapper = String Function(String kind, OvoContext context);

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
  final OvoLocaleMapper localeMapper;
  final OvoThrowMode throwMode;

  final List<OvoIssue> _issues = [];

  OvoContext(
    this.data, {
    this.parent,
    this.segment,
    required this.throwMode,
    required this.localeMapper,
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
      localeMapper: localeMapper,
      throwMode: throwMode,
    );
  }

  @override
  Iterator<OvoIssue> get iterator => _issues.iterator;

  operator []=(String kind, String? message) => _issues.add(
        OvoIssue(kind, message ?? localeMapper(kind, this)),
      );
}
